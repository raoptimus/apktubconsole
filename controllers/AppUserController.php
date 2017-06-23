<?php

namespace app\controllers;

use app\models\users\AppUser;
use app\models\users\AppUserSearch;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * VideoController implements the CRUD actions for appUser model.
 */
class AppUserController extends AccController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access']['rules'][0]['roles'] = ['Manager'];
        return $behaviors;
    }

    /**
     * Lists all appUser models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $this->pageTitle = 'App users';
        $searchModel = new AppUserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all appUser models.
     *
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $this->pageTitle = $model->UserName;

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return AppUser
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = AppUser::findOne(intval($id))) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
