<?php
use app\components\MyHtml as Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Tabs;
use app\models\Language;

/**
 * @var $this yii\web\View
 * @var $model app\models\Ads
 * @var $form ActiveForm
 *
 * */

echo Tabs::widget([
    'encodeLabels' => false,
    'items' => array_merge(
        array_map(function ($lng) use ($model, $form) {
            return [
                'label' => Yii::t('dict', Language::getValue($lng)),
                'content' =>
                    $form->field($model, "TitleForm[{$lng}]")->textInput(
                        [
                            'value' => $model->getLangTitle($lng),
                            'placeholder' => $model->getAttributeLabel('Title'),
                        ]) .
                    $form->field($model, "NameForm[{$lng}]")->textInput(
                        [
                            'placeholder' => $model->getAttributeLabel('Name'),
                        ]) .
                    $form->field($model, "DescForm[$lng]")->textarea(['rows' => 6])
            ];

        }, $model->getActiveLangs()),
        [
            [
                'label' => Html::glyphicon("plus"),
                'items' => array_map(function ($lng) use ($model, $form) {
                    return [
                        'label' => Yii::t('dict', Language::getValue($lng)),
                        'options' => ['class' => 'nav-tab-lang'],
                        'content' =>
                            $form->field($model, "TitleForm[{$lng}]")->textInput(
                                [
                                    'placeholder' => $model->getAttributeLabel('Title'),
                                ]) .
                            $form->field($model, "NameForm[{$lng}]")->textInput(
                                [
                                    'placeholder' => $model->getAttributeLabel('Name'),
                                ]) .
                            $form->field($model, "DescForm[$lng]")->textarea(['rows' => 6])
                    ];
                }, array_diff(Language::getKeys(), $model->getActiveLangs())),
            ]
        ]

    ),
]);

echo '<hr>';