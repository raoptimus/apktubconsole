<?php

namespace app\controllers;

use app\models\files\AdsScreenShot;
use Yii;
use yii\web\Response;
use yii\web\NotFoundHttpException;

class AdsScreenShotController extends AccController
{
    public function actionGetShot($id)
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        $icon = $this->findModel($id);
        Yii::$app->getResponse()->getHeaders()
            ->set('Content-type:', $icon->contentType);

        return $icon->file->getBytes();
    }

    /**
     * @param $id
     * @return AdsScreenShot
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = AdsScreenShot::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}