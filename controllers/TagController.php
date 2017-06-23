<?php

namespace app\controllers;

use Yii;
use app\models\video\TagSearch;
Use app\models\video\Tag;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class TagController extends AccController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access']['rules'][0]['roles'] = ['VideoManager'];
        return $behaviors;
    }

    public function actionIndex()
    {
        $this->pageTitle = 'Tag list';
        $searchModel = new TagSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ]);
    }

    public function actionCreate() {
        $this->pageTitle = 'Create New Tag';
        $model = new Tag();
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
        $this->pageTitle = 'Update Tag ' . $model->FormTitle;

        if (Yii::$app->request->post()) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['index']);
            }
        }
        return $this->render('edit', [
            'model' => $model,
        ]);
    }

    public function actionView($id) {
        $model = $this->findModel($id);
        $this->pageTitle = 'View Tag ' . $model->FormTitle;
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function actionHound($query,$lang) {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $tags = Tag::find()
            ->where(['Title.Language' => $lang])
            ->andWhere(['like','Title.Quote',$query])
            ->limit(10)
            ->all();
        $returnArray = [];
        foreach ($tags as $tag) {
            foreach ($tag->Title as $title) {
                if ($title['Language'] == $lang) {
                    $returnArray[] = ['name'=>$title['Quote']];
                    break;
                }
            }
        }
        return $returnArray;
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
        if (($model = Tag::findOne(['_id' => intval($id)])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


}
