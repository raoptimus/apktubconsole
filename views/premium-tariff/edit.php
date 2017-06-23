<?php

use app\components\MyHtml as Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Button;
use app\models\Currency;
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
            ->label('Название тарифа')
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
                    ->label('Название тарифа')
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



//echo $form->field($model, "Title")->textInput();
echo $form->field($model, "Time")->textInput();
echo $form->field($model, "DisplayPrice")->textInput();
echo $form->field($model, "AproxPrice")->textInput();
echo $form->field($model, "Currency")->dropDownList(Currency::getList());
echo $form->field($model, "Enabled")->dropDownList([true => 'Вкл',false => 'Выкл']);
echo $form->field($model, "PayUrl")->textInput(['type' => 'url'])->hint($model->getAttributeHint('PayUrl'));
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
