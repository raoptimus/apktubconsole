<?php

namespace app\controllers;

use app\models\video\Category;
use app\models\video\Video;
use app\models\video\VideoFilter;
use Yii;
use yii\web\NotFoundHttpException;
use \yii\web\Response;
use \yii\web\ServerErrorHttpException;
use yii\helpers\Url;
use app\components\CustomEvents;

/**
 * VideoController implements the CRUD actions for Video model.
 */
class VideoController extends AccController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access']['rules'][0]['roles'] = ['VideoManager'];
        return $behaviors;
    }

    /**
     * Lists all Video models.
     *
     * @param array $f
     * @return mixed
     */
    public function actionIndex(array $f = null)
    {
        $this->pageTitle = "Video list";

        $filterCats = [];
        $filterCats['*'] = 'All';
        foreach (Category::find()->all() as $cat) {
            $filterCats['c'.$cat->_id] = $cat->Title[0]['Quote'];
        }

        $m = new VideoFilter();
        $m->setScenario("search");
        $m->setAttributes($f);

        Url::remember();

        return $this->render('index', [
            'model' => $m,
            'filterCats' => $filterCats
        ]);
    }

    public function actionPlay($id)
    {
        $model = Video::findOne(intval($id));
        if ($model === null) {
            throw new NotFoundHttpException;
        }

        return $this->render('_play', [
            'model' => $model,
        ]);
    }

    public function actionGetThumb()
    {
        $request = Yii::$app->request;
        $get = $request->get();

        if (isset($get['id']) && isset($get['size']) && !empty($get['id']) && !empty($get['size'])) {
            $response = Yii::$app->getResponse();
            $response->headers->set('Content-Type', 'image/jpeg');
            $response->stream = fopen(Video::getThumbByIdAndSize($get['id'], $get['size']), 'r');

            if (!is_resource($response->stream)) {
                throw new ServerErrorHttpException('file access failed: permission deny');
            }

            \Yii::$app->response->format = Response::FORMAT_RAW;
            $response->send();
        }
    }

    /**
     *
     * @param int $id
     * @param int $index
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionSetIndexThumb($id, $index)
    {
        $v = $this->findModel($id);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return ['result' => $v->saveIndexThumb($index)];
    }

    public function actionDelete($id, $url = null)
    {
        $v = $this->findModel($id);
        $url = $url ? $url : '/video/index';

        if ($v->delete()) {
            $v->trigger(CustomEvents::EVENT_VIDEO_DELETED);
            return $this->flashResult("success", "Видео удалено", $url, ['after' => "reload"]);
        } else {
            return $this->flashResult('error', "delete video error", $url);
        }
    }

    public function actionApprove($id, $url = null)
    {
        $v = $this->findModel($id);
        $url = $url ? $url : Url::toRoute('video/index');

        if ($v->approve()) {
            $v->trigger(CustomEvents::EVENT_VIDEO_PUBLISHED);
            return $this->flashResult("success", "Видео одобрено", $url, ['after' => "reload"]);
        } else {
            return $this->flashResult('error', "approve video error", $url);
        }
    }

    public function actionUp($id, $url = null)
    {
        $v = $this->findModel($id);
        $url = $url ? $url : Url::toRoute('video/index');

        if ($v->up()) {
            $v->trigger(CustomEvents::EVENT_VIDEO_RAISED);
            return $this->flashResult("success", "Видео поднято", $url, ['after' => "toggleActive"]);
        } else {
            return $this->flashResult('error', "up video error", $url);
        }
    }

    public function actionToggleFeatured($id, $url = null)
    {
        $v = $this->findModel($id);
        $url = $url ? $url : Url::toRoute('video/index');

        if ($v->toggleFeatured()) {
            $v->trigger(CustomEvents::EVENT_VIDEO_FEATURED);
            return $this->flashResult("success", "Видео установлено для индекса", $url, ['after' => "toggleActive"]);
        } else {
            return $this->flashResult('error', "toggle featured error", $url);
        }
    }

    /**
     * Displays a single AdminUser model.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionEdit($id)
    {
        $v = $this->findModel($id);
        $this->pageTitle = $v->getLangTitle('ru');
        if ($v->load(Yii::$app->request->post()) && $v->save()) {
            if (Yii::$app->request->post('publish') == 'publish') {
                $v->approve();
            }
            return $this->flashResult("success", Yii::t("dict", "Video successfully updated"), Url::previous());
        } else {
            //this variable is used to be here coz' "empty" can't get function return
            $post = Yii::$app->request->post();
            if (empty($post)) {
                Url::remember(Yii::$app->request->referrer);
            }
            return $this->render('edit', [
                'model' => $v,
            ]);
        }
    }

    /**
     * @param int $id
     * @return Video
     * @throws NotFoundHttpException
     */
    private function findModel($id)
    {
        $video = Video::findOne(intval($id));
        if (!$video) {
            throw new NotFoundHttpException("Video not found");
        }
        return $video;
    }

    private function getMongoValue($value)
    {
        if (is_numeric($value)) {
            return intval($value);
        } else {
            return $value;
        }
    }
}
