<?php

namespace app\controllers;

use app\models\accounting\AccountingVideoFilter;

class AccountingVideoController extends AccController
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access']['rules'][0]['roles'] = ['Manager'];
        return $behaviors;
    }

    public function actionIndex(array $f = null)
    {
        $this->pageTitle = 'Копирайт видео';

        $m = new AccountingVideoFilter();
        $m->setFilter($f);

        return $this->render('index', [
            'model' => $m,
            'dataProvider' => $m->getDataProvider(),
        ]);
    }
}
