<?php

namespace app\controllers;

use app\models\users\Device;
use app\models\users\DeviceSearch;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * VideoController implements the CRUD actions for appUser model.
 */
class DeviceController extends AccController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access']['rules'][0]['roles'] = ['Manager'];
        return $behaviors;
    }

    /**
     * Lists all device models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $this->pageTitle = 'Device list';
        $searchModel = new DeviceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'model' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all appUser models.
     *
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $this->pageTitle = $model->Manufacture . ' ' . $model->Model . ' ' . $model->Os . ' ' . $model->VerOs;

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return Device
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Device::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
