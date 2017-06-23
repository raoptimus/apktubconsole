<?php
/**
 * Created by IntelliJ IDEA.
 * User: ra
 * Date: 22.05.15
 * Time: 22:05
 */

namespace app\controllers;

use app\components\CustomEvents;
use app\models\files\PushIcon;
use app\models\push\TaskSearch;
use Yii;
use yii\web\NotFoundHttpException;
use app\models\push\Task;
use yii\web\UploadedFile;
use yii\web\Response;

class PushTaskController extends AccController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access']['rules'][0]['roles'] = ['Manager', 'PushManager'];
        return $behaviors;
    }

    public function actionDemo()
    {
        exec("/home/ra/TubeServer/bin/push", $o, $r);
        print_r($o);
        Yii::$app->end();
    }

    public function actionIndex($enabled = 1, $deleted = 0)
    {
        $this->pageTitle = "Push task list";

        $searchModel = new TaskSearch();
        $searchModel->Enabled = boolval($enabled);
        $searchModel->Deleted = boolval($deleted);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'title' => Yii::t('dict', 'Push task list'),
        ]);
    }

    public function actionUpdate($id)
    {
        $this->pageTitle = "Update push task";
        Yii::$app->controller->breadcrumbs[] = ['label' => Yii::t('dict', 'Push task list'), 'url' => ['index']];

        $model = $this->findModel($id);
        return $this->editModel($model);
    }

    /**
     * @param int $id
     * @return Task
     * @throws NotFoundHttpException
     */
    private function findModel($id)
    {
        $m = Task::findOne(intval($id));
        if (!$m) {
            throw new NotFoundHttpException("Push task not found");
        }
        return $m;
    }

    private function editModel(Task $model)
    {
        $isNew = $model->isNewRecord;

        $p = Yii::$app->request->post();
        if (!empty($p)) {
            if (isset($p['Options'])) {
                $model->Options = $p['Options'];
            }

            if ($model->load($p)) {
                $iconRaw = UploadedFile::getInstance($model, 'IconFileForm');

                if (!empty($iconRaw)) {
                    $im = new \Imagick($iconRaw->tempName);
                    $im->cropThumbnailImage(128, 128);
                    $im->writeImage($iconRaw->tempName);
                    $iconObj = new PushIcon();
                    $iconObj->file = $iconRaw;
                    $iconObj->size = filesize($iconRaw->tempName);
                    $iconObj->contentType = $iconRaw->type;
                    $iconObj->save();
                    $model->IconFile = $iconObj->id;

                    $type = 'png';
                    if ($iconObj->contentType == 'image/jpeg') {
                        $type = 'jpg';
                    }
                    $model->IconUrl = "/icons/{$iconObj->id}.$type";
                }

                if ($model->save()) {
                    $model->trigger($isNew ? CustomEvents::EVENT_PUSH_CREATED : CustomEvents::EVENT_PUSH_UPDATED);

                    return $this->flashResult(self::FLASH_TYPE_SUCCESS,
                        Yii::t("dict", "Push task is successfully {action}",
                            ["action" => Yii::t("dict", ($isNew ? "created" : "updated"))]),
                        "index"
                    );
                }
            }
        }

        return $this->render('edit', [
            'model' => $model,
        ]);
    }

    public function actionToggleEnabled($id, $enable)
    {
        $model = $this->findModel($id);
        $model->Enabled = boolval($enable);

        if ($model->update()) {
            $model->trigger($model->Enabled ? CustomEvents::EVENT_PUSH_STARTED : CustomEvents::EVENT_PUSH_STOPPED);

            return $this->flashResult(self::FLASH_TYPE_SUCCESS,
                Yii::t("dict", "Push task is {action}",
                    [
                        'action' => Yii::t("dict", ($model->Enabled ? "enabled" : "disabled"))
                    ]),
                "index",
                ['after' => "reload"]
            );
        } else {
            return $this->flashResult(self::FLASH_TYPE_ERROR, Yii::t("dict", "Push task update error"),
                "index",
                ['after' => "reload"]);
        }
    }

    public function actionToggleDeleted($id)
    {
        $model = $this->findModel($id);

        $model->Enabled = false;
        $model->Deleted = !boolval($model->Deleted);

        if ($model->update()) {
            $model->trigger($model->Deleted ? CustomEvents::EVENT_PUSH_DELETED : CustomEvents::EVENT_PUSH_RESTORED);

            return $this->flashResult(self::FLASH_TYPE_SUCCESS,
                Yii::t("dict", "Push task is {action}",
                    [
                        'action' => Yii::t("dict", ($model->Deleted ? "deleted" : "restored"))
                    ]),
                "index",
                ['after' => "reload"]
            );
        } else {
            return $this->flashResult(self::FLASH_TYPE_ERROR, Yii::t("dict", "Push task update error"),
                "index",
                ['after' => "reload"]);
        }
    }

    public function actionCreate($ActionForm = 0)
    {
        $this->pageTitle = "Create push task";
        Yii::$app->controller->breadcrumbs[] = ['label' => Yii::t('dict', 'Push task list'), 'url' => ['index']];

        $model = new Task();
        $model->ActionForm = $ActionForm;
        return $this->editModel($model);
    }

    public function actionGetIcon($id)
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        $icon = $this->findIcon($id);
        Yii::$app->getResponse()->getHeaders()
            ->set('Content-type:', $icon->contentType);
        return $icon->file->getBytes();
    }

    /**
     * @param int $id
     * @return Task
     * @throws NotFoundHttpException
     */
    private function findIcon($id)
    {
        $m = PushIcon::findOne($id);
        if (!$m) {
            throw new NotFoundHttpException("Push task not found");
        }
        return $m;
    }

    public function actionRemoveIcon($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $task = $this->findModel($id);
        $task->removeIcon();
        return ['success' => true];
    }
}