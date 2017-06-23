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
echo $form->errorSummary($model); ?>
<?= $form->field($model, "Title")->textInput() ?>
<?= $form->field($model, "StorageType")->dropDownList(['swift' => 'swift', 'ftp' => 'ftp', 'rsync' => 'rsync']) ?>
<?= $form->field($model, "Username")->textInput() ?>
    <section class="swift-fields">
        <?= $form->field($model, "Tenant")->textInput() ?>
    </section>
    <section class="non-swift-fields">
        <?= $form->field($model, "Password")->textInput() ?>
    </section>
<?= $form->field($model, "Domain")->textInput(); ?>
    <section class="swift-fields">
        <?php
        echo $form->field($model, "DomainId")->textInput();
        echo $form->field($model, "ApiKey")->textInput();
        echo $form->field($model, "AuthUrl")->textInput();
        ?>
    </section>
    <section class="non-swift-fields">
        <?= $form->field($model, "Port")->textInput(); ?>
    </section>
<?= $form->field($model, "Container")->textInput() ?>
<?php
echo Button::widget([
    'label' => Yii::t('dict', $model->isNewRecord ? 'Create' : 'Save'),
    'options' => ['type' => 'submit', 'class' => ['col-sm-offset-2', 'btn-primary']],
]);
?>
<?php
ActiveForm::end();
