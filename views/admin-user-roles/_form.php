<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\AdminUserRoles */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="admin-user-roles-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'description')->textInput(['maxlength' => 255]) ?>

    <?php echo $form->field($model, 'permissions')->checkboxList($model->getAllPermissions())->label('Child permissions') ?>

    <?php echo $form->field($model, 'roles')->checkboxList($model->getAllRoles($model->name))->label('Child roles') ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('dict', 'Create') : Yii::t('dict', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
