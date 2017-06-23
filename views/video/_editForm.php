<?php
use app\components\MyHtml as Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Tabs;
use app\models\Language;

/**
 * @var $this yii\web\View
 * @var $model app\models\video\Video
 * @var $form ActiveForm
 *
 * */

echo Tabs::widget([
    'encodeLabels' => false,
    'items' => array_merge(
        array_map(function ($lng) use ($model, $form) {
            return [
                'label' => Yii::t('dict',Language::getValue($lng)),
                'content' =>
                    $form->field($model, "TitleForm[{$lng}]")->textInput(
                        [
                            'value' => $model->getLangTitle($lng),
                            'placeholder' => $model->getAttributeLabel('Title'),
                        ]).
                    $form->field($model, "DescForm[$lng]")->textarea(['rows'=>"6"]) .
                    $form->field($model, "TagsForm[{$lng}]")
                        ->textInput([
                            "class" => "form-control myTagsInput",
                            "data-lang" => $lng
                        ])
                ,
            ];

        }, $model->getActiveLangs()),
        [
            [
                'label' => Html::glyphicon("plus"),
                'items' => array_map(function ($lng) use ($model, $form) {
                    return [
                        'label' => Yii::t('dict',Language::getValue($lng)),
                        'options' => ['class' => 'nav-tab-lang'],
                        'content' =>
                            $form->field($model, "TitleForm[{$lng}]")->textInput(
                                [
                                    'placeholder' => $model->getAttributeLabel('Title'),
                                ]).
                            $form->field($model, "DescForm[$lng]")->textarea() .
                            $form->field($model, "TagsForm[{$lng}]")
                                ->textInput([
                                    "class" => "form-control myTagsInput",
                                    "value" => '',
                                    "data-lang" => $lng
                                ])
                                ->label('Tag name')
                        ,
                    ];
                }, array_diff(Language::getKeys(), $model->getActiveLangs())),
            ]
        ]

    ),
]);

echo '<hr>';