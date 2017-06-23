<?php

namespace app\controllers;

use app\models\Ads;
use app\models\AdsSearch;
use Yii;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

class AdsController extends AccController
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access']['rules'][0]['roles'] = ['Manager'];
        return $behaviors;
    }

    public function actionIndex()
    {
        $this->pageTitle = 'Ads list';
        $searchModel = new AdsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $this->pageTitle = 'Create Ads';
        $model = new Ads();
        if ($model->load(Yii::$app->request->post())) {
            $icon = UploadedFile::getInstance($model, 'IconForm');
            if ($icon) {
                $model->setIconForm($icon);
            }
            $screenShots = UploadedFile::getInstances($model, 'ScreenShotsForm');
            if (!empty($screenShots)) {
                $model->setScreenShotsForm($screenShots);
            }

            if ($model->save()) {
                return $this->flashResult("success", "Объявление успешно создано", Url::toRoute(['update', 'id' => $model->id]));
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $this->pageTitle = 'Редактирование ' . $model->getLangAttribute();
        if ($model->load(Yii::$app->request->post())) {
            $file = UploadedFile::getInstance($model, 'IconForm');
            if ($file) {
                $model->setIconForm($file);
            }

            $screenShots = UploadedFile::getInstances($model, 'ScreenShotsForm');
            if (!empty($screenShots)) {
                $model->setScreenShotsForm($screenShots);
            }

            if ($model->save()) {
                $this->flashResult("success", "Объявление успешно обновлено");
            }
        }

        return $this->render('edit', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return Ads
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Ads::findOne(['_id' => intval($id)])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionSortShots($id, $o) {
        $model = $this->findModel($id);

        Yii::$app->response->format = Response::FORMAT_JSON;
        $order = json_decode($o);

        return $model->sortShots($order);
    }

    public function actionSort() {
        Yii::$app->response->format = Response::FORMAT_RAW;
        $newOrder = json_decode(Yii::$app->request->post('items'), true);
        return Ads::switchOrder($newOrder);
    }


    public function actionDelete($id) {
        $model = $this->findModel($id);
        if ($model->delete()) {
            return $this->flashResult("success", "Объявление успешно удалено", Url::toRoute(['index']));
        }
        return $this->flashResult("warning", "Не удалось удалить объявление", Url::toRoute(['update', 'id' => $model->id]));
    }

    public function actionDeleteIcon($id = '', $model = '')
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = $this->findModel($model);
        try {
            if ($model->deleteIcon($id)) {
                return $this->flashResult("success", "Картинка успешно удалена", Url::toRoute(['update', 'id' => $model->id]));
            } else {
                return $this->flashResult("warning", "Произошла ошибка. Попробуйте повторить операцию позднее", Url::toRoute(['update', 'id' => $model->id]));
            }
        } catch (\Exception $e) {
            return $this->flashResult("warning", "Произошла ошибка. " . $e->getMessage() , Url::toRoute(['update', 'id' => $model->id]));
        }
    }
}
