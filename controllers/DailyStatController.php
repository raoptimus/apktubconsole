<?php

namespace app\controllers;

use Yii;
use app\models\stat\DailyStatSearch;

/**
 * DailyStatController implements the CRUD actions for DailyStat model.
 */
class DailyStatController extends AccController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access']['rules'][0]['roles'] = ['Manager'];
        $behaviors['access']['rules'][0];
        $behaviors['access']['rules'][] = [
            'allow' => true,
            'roles' => ['Partner'],
        ];
        return $behaviors;
    }

    /**
     * Lists all DailyStat models.
     * @param array $f
     * @return string
     */
    public function actionIndex(array $f = null)
    {
        $this->pageTitle = "Daily statistics";

        $m = new DailyStatSearch();
        $m->setScenario("search");
        $m->setAttributes($f);

        if (Yii::$app->user->can('bePartner')) {
            $user = Yii::$app->user->getIdentity();
            $f['Partner'] = $user->username;
        }

        $m->getDictionaries();

        return $this->render('index', [
            'model' => $m,
            'dataProvider' => $m->getDataProvider(),
        ]);
    }
}
