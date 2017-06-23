<?php

namespace app\controllers;

use app\models\video\Channel;
use app\models\video\ChannelSearch;
use Yii;
use app\models\video\TagSearch;
Use app\models\video\Tag;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ChannelController extends AccController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access']['rules'][0]['roles'] = ['VideoManager'];
        return $behaviors;
    }

    public function actionIndex()
    {
        $this->pageTitle = 'Channel list';
        $searchModel = new ChannelSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ]);
    }

    public function actionCreate() {
        $this->pageTitle = 'Create Channel';
        $model = new Channel();
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
        $this->pageTitle = 'Update Tag ' . $model->Title;

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

    /**
     * Finds the AdminUser model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Tag the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Channel::find()->where(['_id'=>intval($id)])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


}
