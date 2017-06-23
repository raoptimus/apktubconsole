<?php

namespace app\controllers;

use app\models\storage\Storage;
use app\models\storage\StorageSearch;
use Yii;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

class StorageController extends AccController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access']['rules'][0]['roles'] = ['Admin'];
        return $behaviors;
    }

    public function actionIndex()
    {
        $this->pageTitle = 'Список хранилищ';
        $searchModel = new StorageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ]);
    }

    public function actionCreate()
    {
        $this->pageTitle = Yii::t('dict', 'Create new storage');
        $this->breadcrumbs = [
            ['label' => 'Главная', 'url' => ['dashboard/index']],
            ['label' => 'Список хранилищ', 'url' => ['storage/index']]
        ];
        $model = new Storage();
        if (Yii::$app->request->post()) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['index']);
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $this->pageTitle = 'Update storage ' . $model->Title;
        $this->breadcrumbs = [
            ['label' => 'Главная', 'url' => ['dashboard/index']],
            ['label' => 'Список хранилищ', 'url' => ['storage/index']]
        ];

        if (Yii::$app->request->post()) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['index']);
            }
        }
        return $this->render('edit', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return Storage
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Storage::findOne(intval($id))) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionRemove($id)
    {
        $model = $this->findModel($id);
        $url = Url::toRoute('storage/index');
        if ($model->delete()) {
            return $this->flashResult("success", "Хранилище удалено", $url, ['after' => "reload"]);
        } else {
            return $this->flashResult("error", "Произошла ошибка при удалении", $url, ['after' => "reload"]);
        }
    }
}
