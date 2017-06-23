<?php

namespace app\controllers;

use Yii;
use app\models\files\AdsIcon;
use yii\web\Response;
use yii\web\NotFoundHttpException;

class AdsIconController extends AccController
{
    public function actionGetIcon($id)
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        $icon = $this->findModel($id);
        Yii::$app->getResponse()->getHeaders()
            ->set('Content-type:', $icon->contentType);

        return $icon->file->getBytes();
    }

    /**
     * @param $id
     * @return AdsIcon
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = AdsIcon::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}