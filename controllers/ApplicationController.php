<?php

namespace app\controllers;

use app\models\push\Action;
use Yii;
use app\models\Application;
use app\models\ApplicationSearch;
use yii\web\NotFoundHttpException;
use yii\helpers\Url;

/**
 * ApplicationController implements the CRUD actions for Application model.
 */
class ApplicationController extends AccController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access']['rules'][0]['roles'] = ['Manager'];
        return $behaviors;
    }

    /**
     * Lists all Application models.
     * @return mixed
     */
    public function actionIndex($status = 0)
    {
        $searchModel = new ApplicationSearch();
        $searchModel->Status = intval($status);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $this->pageTitle = 'Applications';

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $id
     * @return mixed
     */
    public function actionRelease($id)
    {
        $model = $this->findModel($id);
        $model->Status = 1;
        $model->ReleaseDate = new \MongoDate();

        if ($model->save()) {
            return $this->flashResult("success", "Релиз обновлён",
                Url::toRoute(['push-task/create', ['ActionForm' => Action::NotifyUpgrade()]]));
        } else {
            $error_string = count($model->errors) > 0 ? "<pre>" . print_r($model->errors, true) . "</pre>" : "";
            return $this->flashResult('error', "Не удалось выложить приложение в релиз! " . $error_string,
                Url::toRoute(['application/index']));
        }
    }

    /**
     * Finds the Application model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Application the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Application::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Creates a new Application model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Application();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionRemove($id)
    {
        $model = $this->findModel($id);
        if ($model->Status == 1) {
            return $this->flashResult("error", "Нельзя удалить выпущенное приложение!", Url::toRoute(['index']));
        } else {
            if ($model->remove()) {
                return $this->flashResult("success", "Приложение успешно удалено", Url::toRoute(['index']));
            } else {
                return $this->flashResult("error", "Не удалось удалить приложение", Url::toRoute(['index']));
            }
        }
    }

    /**
     * Updates an existing Application model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionDownload($id)
    {
        $model = $this->findModel($id);
        $file = $model->File;

        $options = [
            'mimeType' => $file->file['contentType']
        ];
        return Yii::$app->response->sendStreamAsFile($file->getResource(), basename($file->file['filename']), $options);
    }

    public function formName()
    {
        return "f";
    }
}
