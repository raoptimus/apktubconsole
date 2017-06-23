<?php

namespace app\controllers;

use app\models\storage\Files;
use app\models\storage\FilesSearch;
use Yii;
use yii\web\NotFoundHttpException;

class StorageFilesController extends AccController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access']['rules'][0]['roles'] = ['Admin'];
        return $behaviors;
    }

    public function actionIndex()
    {
        $this->pageTitle = 'Список файлов';
        $searchModel = new FilesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $counts = Files::getCounts();
        $weight = Files::getWeight();

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'counts' => $counts,
            'weight' => $weight
        ]);
    }

    public function actionView($id)
    {
        $this->pageTitle = 'Общая информация:';
        $model = $this->findModel($id);

        return $this->render('view', [
            'model' => $model,
        ]);
    }


    protected function findModel($id)
    {
        if (($model = Files::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


}
