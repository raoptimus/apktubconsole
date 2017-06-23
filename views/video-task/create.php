<?php
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Button;
use yii\web\UploadedFile;

$form = ActiveForm::begin([
    'options' => ['enctype' => 'multipart/form-data'],
    'layout' => 'horizontal',
    'fieldConfig' => [
        'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
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

echo $form->field($model, "Url")->textInput(['type' => 'url']);
echo $form->field($model, "File")->fileInput();
echo $form->field($model, "Title")->textInput();
echo $form->field($model, "TagsForm")->textInput();
echo $form->field($model, "ModelsForm")->textInput();
echo $form->field($model, "ProjectsForm")->textInput();

echo $form->field($model, "Length")->textInput();
echo $form->field($model, "Offset")->textInput();


?>
<div class="form-group">
    <div class="col-sm-offset-2 col-sm-8">
        <?php
        echo Button::widget([
            'label' => 'Создать',
            'options' => ['type' => 'submit', 'class' => 'btn-primary'],
        ]);
        ?>
    </div>
</div>
