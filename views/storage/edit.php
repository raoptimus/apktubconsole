<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Button;

/**
 * @var $this yii\web\View
 * @var $model app\models\storage\Storage
 */

$form = ActiveForm::begin([
    'layout' => 'horizontal',
    'fieldConfig' => [
        'template' => "<br>{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
        'horizontalCssClasses' => [
            'label' => 'col-sm-2',
            'offset' => 'col-sm-offset-2',
            'wrapper' => 'col-sm-8',
            'error' => '',
            'hint' => '',
        ],
    ],
]);
echo $form->errorSummary($model);
echo $form->field($model, "Title")->textInput();
echo $form->field($model, "StorageType")->textInput(['disabled' => 'disabled']);
echo $form->field($model, "Username")->textInput();
echo $model->StorageType == 'swift' ? $form->field($model, "Tenant")->textInput() : $form->field($model, "Password")->textInput();
echo $form->field($model, "Domain")->textInput();

if ($model->StorageType == 'swift') {
    echo $form->field($model, "DomainId")->textInput();
    echo $form->field($model, "ApiKey")->textInput();
    echo $form->field($model, "AuthUrl")->textInput();
} else {
    echo $form->field($model, "Port")->textInput();
}

echo $form->field($model, "Container")->textInput();
echo Button::widget([
    'label' => Yii::t('dict', $model->isNewRecord ? 'Create' : 'Save'),
    'options' => ['type' => 'submit', 'class' => ['col-sm-offset-2', 'btn-primary']],
]);
ActiveForm::end();