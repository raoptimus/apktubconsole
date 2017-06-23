<?php

namespace app\controllers;

use app\models\video\Actor;
use app\models\video\ActorSearch;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ActorController extends AccController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access']['rules'][0]['roles'] = ['VideoManager'];
        return $behaviors;
    }

    public function actionIndex()
    {
        $this->pageTitle = 'Actor list';
        $searchModel = new ActorSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ]);
    }

    public function actionCreate() {
        $this->pageTitle = 'Create Actor';
        $model = new Actor();
        if (Yii::$app->request->post()) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['index']);
            }
        }
        return $this->render('edit', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $this->pageTitle = 'Update Actor ' . $model->Name;

        if (Yii::$app->request->post()) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['index']);
            }
        }
        return $this->render('edit', [
            'model' => $model,
        ]);
    }

    public function actionRemove($id) {
        $model = $this->findModel($id);
        $model->delete();
        //TODO: удаление канала из кейвордов видео
        return $this->redirect(['index']);
    }

    public function actionHound($q) {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return Actor::find()
            ->where(['like','Name',$q])
            ->limit(10)
            ->all();
    }

    /**
     * Finds the AdminUser model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Actor the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Actor::find()->where(['_id'=>intval($id)])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


}
