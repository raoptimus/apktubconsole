<?php
/**
 * Created by IntelliJ IDEA.
 * User: ra
 * Date: 22.05.15
 * Time: 22:05
 */

namespace app\controllers;

use app\components\CustomEvents;
use app\models\files\PushIcon;
use app\models\push\TaskSearch;
use Yii;
use yii\web\NotFoundHttpException;
use app\models\push\Task;
use yii\web\UploadedFile;
use yii\web\Response;

class PushIconController extends AccController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        return $behaviors;
    }

    public function actionGetIcon($id) {
        Yii::$app->response->format = Response::FORMAT_RAW;
        $icon = $this->findIcon($id);
        Yii::$app->getResponse()->getHeaders()
            ->set('Content-type:', $icon->contentType);
        return $icon->file->getBytes();
    }

    /**
     * @param int $id
     * @return Task
     * @throws NotFoundHttpException
     */
    private function findIcon($id)
    {
        $m = PushIcon::findOne($id);
        if (!$m) {
            throw new NotFoundHttpException("Push task not found");
        }
        return $m;
    }
}