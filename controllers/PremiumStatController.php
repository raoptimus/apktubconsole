<?php

namespace app\controllers;

use app\models\premium\Stat;
use Yii;

class PremiumStatController extends AccController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access']['rules'][0]['roles'] = ['Admin'];
        return $behaviors;
    }

    public function actionIndex()
    {
        $this->pageTitle = Yii::t('dict','Premium statistic');
        $model = new Stat();
        $dataProvider = $model->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'model' => $model
        ]);
    }
}
