<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\AdminUserRoles;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="admin-user-form">

<?php $form = ActiveForm::begin();

echo($form->errorSummary($model));

echo $form->field($model, 'username')->textInput(['maxlength' => 255]);

echo $form->field($model, 'NewPassword')->passwordInput(['maxlength' => 255]);

if (Yii::$app->user->can('bePartner')) {
    echo $form->field($model, 'PostBackUrl')->textInput(['maxlength' => 255]);
}

echo $form->field($model, 'RolesList')->checkboxList(AdminUserRoles::getAllRoles());
?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('dict', 'Create') : Yii::t('dict', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
