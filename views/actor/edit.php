<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Button;

/**
 * @var $this yii\web\View
 * @var $model app\models\video\Actor
 */

$form = ActiveForm::begin([
    'layout' => 'default',
    'options' => ['class' => 'well'],
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

echo $form->field($model, "Name")->textInput([])
?>
    <div class="form-group">
        <?php
        echo Button::widget([
            'label' => Yii::t('dict', $model->isNewRecord ? 'Create' : 'Save'),
            'options' => ['type' => 'submit', 'class' => 'btn-primary'],
        ]);
        ?>
    </div>
<?php
ActiveForm::end();
