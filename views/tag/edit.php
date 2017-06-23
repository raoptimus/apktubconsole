<?php

use app\components\MyHtml as Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Button;
use yii\bootstrap\Tabs;
use app\models\Language;

/**
 * @var $this yii\web\View
 * @var $model app\models\video\Tag
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


$items = [];
foreach ($model->TitleArray as $lang_key => $title) {
    $items[] = [
        'label' => Language::getValue($lang_key),
        'content' => $form
            ->field($model, "TitleArray[{$lang_key}]")
            ->textInput([
                "value" => $title
            ])
            ->label('Tag name')
    ];
}

$items[] = [
    'label' => Html::glyphicon("plus"),
    'items' => array_map(
        function ($e, $key) use ($form, $model){
            return [
                'label' => $e,
                'options' => [
                    'class' => 'nav-tab-lang'
                ],
                'content' => $form
                    ->field($model, "TitleArray[{$key}]")
                    ->textInput([
                        "value" => ''
                    ])
                    ->label('Tag name')
            ];
        },
        $model->EmptyTitleArray,
        array_keys($model->EmptyTitleArray)
    )
];

echo Tabs::widget([
    'encodeLabels' => false,
    'items' => $items
]);

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
