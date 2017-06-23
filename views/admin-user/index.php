<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $userDataProvider yii\data\ActiveDataProvider */
/* @var $rolesDataProvider yii\data\ActiveDataProvider */
/* @var $permissionsDataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('dict', 'Admin Users');
$this->params['breadcrumbs'][] = $this->title;

$adminUsersTable = $this->render('blocks/edit_user', [
    'dataProvider' => $userDataProvider,
]);

$adminRolesTable = $this->render('blocks/edit_roles', [
    'dataProvider' => $rolesDataProvider,
]);

$permissionsRolesTable = $this->render('blocks/edit_permissions', [
    'dataProvider' => $permissionsDataProvider,
]);

echo Tabs::widget([
        'items' => [
            [
                'label' => 'Edit Users',
                'content' => $adminUsersTable,
                'active' => true
            ],
            [
                'label' => 'Edit Roles',
                'content' => $adminRolesTable,
            ],
            [
                'label' => 'Edit Permissions',
                'content' => $permissionsRolesTable,
            ],
        ],
    ]);