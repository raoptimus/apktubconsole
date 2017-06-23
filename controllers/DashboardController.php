<?php
/**
 * Created by IntelliJ IDEA.
 * User: ra
 * Date: 19.05.15
 * Time: 3:28
 */

namespace app\controllers;
use Yii;

class DashboardController extends AccController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access']['rules'][0]['roles'] = ['Manager'];
        $behaviors['access']['rules'][0];
        $behaviors['access']['rules'][] = [
            'allow' => true,
            'roles' => ['@'],
        ];
        return $behaviors;
    }

    public function actionIndex()
    {
        $this->pageTitle = 'Welcome, ' . Yii::$app->user->getIdentity()->username;
        return $this->render("index");
    }

    public function actionLinkManager()
    {
        $this->pageTitle = 'Конструктор ссылки';
        return $this->render('link-constructor');
    }
}