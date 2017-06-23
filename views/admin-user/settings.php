<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */

?>
<div class="admin-user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="admin-user-form">

        <?php $form = ActiveForm::begin();

        echo $form->field($model, 'username')->textInput(['maxlength' => 255,'readonly' => true]);

        echo $form->field($model, 'NewPassword')->passwordInput(['maxlength' => 255]);

        if (Yii::$app->user->can('bePartner') || Yii::$app->user->can('beManager')) {
            echo $form->field($model, 'PostBackUrl')->textInput(['maxlength' => 255]);
            echo Html::tag('b','Обязательные мета-теги:');
            echo Html::ul([
                '{OFFER_ID}',
                '{AFFILIATE_ID}',
            ]);
            echo Html::tag('b','Доступные мета-теги:');
            echo Html::ul([
                '{TRANSACTION_ID}',
                '{DEVICE_MODEL}',
                '{DEVICE_OS}',
                '{DEVICE_BRAND}',
                '{DEVICE_VER}',
            ]);
        }

        ?>

        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('dict', 'Create') : Yii::t('dict', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
    <div class="admin-user-form">



    </div>
</div>
