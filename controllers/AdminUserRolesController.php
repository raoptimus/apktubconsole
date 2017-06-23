<?php

namespace app\controllers;

use Yii;
use app\models\AdminUserRoles;
use yii\web\NotFoundHttpException;

/**
 * AdminUserRolesController implements the CRUD actions for AdminUserRoles model.
 */
class AdminUserRolesController extends AccController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access']['rules'][0]['roles'] = ['Admin'];
        return $behaviors;
    }

    /**
     * Creates a new AdminUserRoles model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $this->pageTitle = 'Create new Role';

        $model = new AdminUserRoles();

        if ($model->load(Yii::$app->request->post())) {
            $auth = Yii::$app->authManager;
            $newRole = $auth->createRole($model->name);
            $newRole->description = $model->description;
            if ($auth->add($newRole)) {
                return $this->redirect(['admin-user/index']);
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionCreatePermission()
    {
        $this->pageTitle = 'Create new Permission';

        $model = new AdminUserRoles();

        if ($model->load(Yii::$app->request->post())) {
            $auth = Yii::$app->authManager;
            $newPermission = $auth->createPermission($model->name);
            $newPermission->description = $model->description;

            if ($auth->add($newPermission)) {
                return $this->redirect(['admin-user/index']);
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing AdminUserRoles model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $name = $model->name;
        $this->pageTitle = 'Edit ' . $model->name;

        if ($model->load(Yii::$app->request->post())) {
            $auth = Yii::$app->authManager;
            $newRole = $auth->getRole($name);
            $newRole->description = $model->description;
            $newRole->name = $model->name;

            if ($auth->update($name, $newRole)) {

                $post = Yii::$app->request->post();
                $permissions = $post['AdminUserRoles']['permissions'];
                $roles = $post['AdminUserRoles']['roles'];

                $auth->removeChildren($newRole);

                if (!empty($permissions)) {
                    foreach ($permissions as $permission) {
                        $permissionObj = $auth->getPermission((string) $permission);
                        $auth->addChild($newRole,$permissionObj);
                    }
                }

                if (!empty($roles)) {
                    foreach ($roles as $role) {
                        $roleObj = $auth->getRole($role);
                        $auth->addChild($newRole,$roleObj);
                    }
                }
                return $this->redirect(['admin-user/index']);
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdatePermission($id)
    {
        $model = $this->findModel($id);
        $name = $model->name;
        $this->pageTitle = 'Edit ' . $model->name;

        if ($model->load(Yii::$app->request->post())) {
            $auth = Yii::$app->authManager;
            $newPermission = $auth->getPermission($name);
            $newPermission->description = $model->description;
            $newPermission->name = $model->name;

            if ($auth->update($name, $newPermission)) {
                return $this->redirect(['admin-user/index']);
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }
    /**
     * Deletes an existing AdminUserRoles model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $name
     * @return mixed
     */
    public function actionRemove($name)
    {
        $auth = Yii::$app->authManager;
        $role = $auth->getRole($name);
        if (is_null($role)) {
            $role = $auth->getPermission($name);
        }
        $auth->remove($role);

        return $this->redirect(['admin-user/index']);
    }

    /**
     * Finds the AdminUserRoles model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return AdminUserRoles the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AdminUserRoles::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
