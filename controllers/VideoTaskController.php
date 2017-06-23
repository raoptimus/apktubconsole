<?php

namespace app\controllers;

use Yii;
use yii\web\UploadedFile;
use app\models\video\VideoTaskForm;

class VideoTaskController extends AccController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access']['rules'][0]['roles'] = ['VideoManager'];
        return $behaviors;
    }

    public function actionIndex($state = "")
    {
        $this->pageTitle = "Tasks";
        return $this->render('index', [
            "state" => $state,
            "tasks" => Yii::$app->videoManager->getTaskList($state),
        ]);
    }

    public function actionCreate()
    {
        $this->pageTitle = "Create task";
        $model = new VideoTaskForm();
        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post()))
        {
            $model->File = UploadedFile::getInstance($model, "File");
            if ($model->File)
            {
                $model->scenario = "upload";
                if ($model->upload() && $model->send())
                {
                    return $this->redirect(["index"]);
                }
            }
            else if ($model->validate() && $model->send())
            {
                return $this->redirect(["index"]);
            }
        }
        return $this->render('create', ["model" => $model]);
    }

    public function actionRetry($id)
    {
        try {
            Yii::$app->videoManager->retryTask($id);
            return $this->success("Success");
        } catch(\Exception $e) {
            return $this->error($e);
        }
    }

    public function actionKill($id)
    {
        try {
            Yii::$app->videoManager->killTask($id);
            return $this->success("Killed");
        } catch(\Exception $e) {
            return $this->error($e);
        }
    }

    public function actionRestart($id)
    {
        try {
            Yii::$app->videoManager->restartTask($id);
            return $this->success("Restarted");
        } catch(\Exception $e) {
            return $this->error($e);
        }
    }

    private function success($comment)
    {
        return $this->flashResult(self::FLASH_TYPE_SUCCESS, $comment, "index", ["after" => "reload"]);

    }

    private function error(\Exception $e)
    {
        return $this->flashResult(self::FLASH_TYPE_ERROR, $e->getMessage());
    }
}
