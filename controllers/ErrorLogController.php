<?php

namespace app\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use app\models\ELog;
use app\models\ELogSearch;

class ErrorLogController extends AccController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access']['rules'][0]['roles'] = ['Admin'];
        return $behaviors;
    }

    public function actionIndex()
    {
        $this->pageTitle = 'Список ошибок';
        $searchModel = new ELogSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ]);
    }

    protected function findModel($id)
    {
        if (($model = ELog::find()->where(['_id'=>$id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


}
