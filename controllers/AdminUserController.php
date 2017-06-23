<?php

namespace app\controllers;

use app\models\AdminUserRoles;
use Yii;
use app\models\User;
use yii\web\NotFoundHttpException;
Use yii\helpers\Url;

/**
 * AdminUserController implements the CRUD actions for AdminUser model.
 */
class AdminUserController extends AccController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access']['rules'][0]['roles'] = ['Admin'];
        return $behaviors;
    }

    /**
     * Lists all AdminUser models.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->pageTitle = 'Edit Users and Roles';

        $searchModel = new User();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $rolesDataProvider = AdminUserRoles::getDataProvider();

        $permissionsDataProvider = AdminUserRoles::getPermissionsDataProvider();


        return $this->render('index', [
            'userDataProvider' => $dataProvider,
            'rolesDataProvider' => $rolesDataProvider,
            'permissionsDataProvider' => $permissionsDataProvider
        ]);
    }

    /**
     * Creates a new AdminUser model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $this->pageTitle = 'Create New User';
        $model = new User();
        if (Yii::$app->request->post()) {
            if ($model->load(Yii::$app->request->post())) {
                if (!empty($model->NewPassword)) {
                    $model->setPassword($model->NewPassword);
                }
                if ($model->save()) {
                    $model->setRolesList(Yii::$app->request->post()['User']['RolesList']);
                    return $this->redirect(['index']);
                }
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing AdminUser model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $this->pageTitle = 'Edit ' . $model->username;

        if (Yii::$app->request->post()) {
            if ($model->load(Yii::$app->request->post())) {

                if (!empty($model->NewPassword)) {
                    $model->setPassword($model->NewPassword);
                }
                if ($model->save()) {
                    return $this->redirect(['index']);
                }
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionSettings()
    {
        $user = Yii::$app->user->getIdentity();
        $model = $this->findModel($user->getId());

        $this->pageTitle = 'Edit ' . $model->username;

        if (Yii::$app->request->post()) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->getOldAttribute('username') != $model->getAttribute('username')) {
                    \Yii::$app->session->setFlash('error', 'Не пытайся меня обмануть!');
                } else {
                    if (!empty($model->NewPassword)) {
                        $model->setPassword($model->NewPassword);
                    }
                    if ($model->save()) {
                        \Yii::$app->session->setFlash('success', 'Настройки сохранены');
                    }
                }
            }
        }
        return $this->render('settings', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing AdminUser model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionRemove($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the AdminUser model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionToggleBlock($id) {
        $model = User::findOne($id);
        if ($model->Blocked == 1) {
            if ($model->unBlock()) {
                return $this->flashResult("success", "Пользователь разблокирован", Url::toRoute(['index']));
            } else {
                return $this->flashResult("error", "Пользователя разблокировать не удалось", Url::toRoute(['index']));
            }
        } else {
            if ($model->block()) {
                return $this->flashResult("success", "Пользователь заблокирован", Url::toRoute(['index']));
            } else {
                return $this->flashResult("error", "Пользователя заблокировать не удалось", Url::toRoute(['index']));
            }
        }
    }

}
