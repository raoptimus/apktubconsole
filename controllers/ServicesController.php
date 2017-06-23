<?php
namespace app\controllers;

use app\components\Transliterator;
use Yii;
use yii\web\Response;

class ServicesController extends AccController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        return $behaviors;
    }

    public function actionTranslit($q = '',$lang = 'ru') {
        Yii::$app->response->format = Response::FORMAT_RAW;
        return Transliterator::alterate($q,$lang);
    }
}