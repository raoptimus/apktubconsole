<?php
use yii\helpers\Url;
/**
 * @var $this yii\web\View
 * @var $model app\models\video\Category
 * @var $lng string
 * @var $form \yii\bootstrap\ActiveForm
 */

echo $form->field($model, "TitleForm[{$lng}]")->textInput([
    'value' => empty($model->Title) ? '' : $model->getLangAttr('Title',$lng),
    'placeholder' => $model->getAttributeLabel('Title'),
    'data-lang' => $lng,
    'data-url' => Url::toRoute('services/translit'),
    'class' => 'form-control translitTrigger'
]);

echo $form->field($model, "SlugForm[{$lng}]")->textInput([
    'value' => empty($model->SlugForm) ? '' : $model->getLangAttr('Slug',$lng),
    'placeholder' => $model->getAttributeLabel('Title'),
    'readOnly' => true,
    'class' => 'form-control translitGetter_' . $lng
]);

echo $form->field($model, "ShortDescForm[{$lng}]")->textarea();
echo $form->field($model, "LongDescForm[{$lng}]")->textarea();
