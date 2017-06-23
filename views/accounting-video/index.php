<?php

use app\components\MyHtml as Html;
use yii\grid\GridView;
use yii\helpers\Url;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;
use yii\web\View;
use app\models\User;
use yii\bootstrap\Button;

/**
 * @var $this yii\web\View
 * @var $model app\models\stat\DailyStatSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 * */
?>

<?php Pjax::begin();
$form = ActiveForm::begin([
    'layout' => 'inline',
    'options' => ['data-pjax' => true, 'class' => 'well well-sm'],
    'action' => Url::to([$this->context->action->id]),
    'method' => 'get',
    'fieldConfig' => [
        'template' => "\n{input}&nbsp\n",
    ],
]);

echo $form->field($model, 'DateRange', [
    'inputTemplate' => '<div class="input-group"><span class="input-group-addon">' .
        Html::glyphicon('calendar') .
        '</span>{input}</div>',
])->widget(DateRangePicker::classname(), [
    'presetDropdown' => true,
    'language' => 'ru-RU',
    'pluginEvents' => [
        "apply.daterangepicker" => "function(){
                $('form.well').submit();
            }",
    ],
    'options' => [
        'style' => 'width: 230px;',
        'class' => 'form-control'
    ],
    'pluginOptions' =>[
        'locale'=>['format'=>'YYYY-MM-DD']
    ]
]);

echo $form->field($model, "Author")->textInput(['placeholder' => $model->getAttributeLabel('Author')]);

echo Button::widget([
    'label' => 'Apply',
    'options' => ['type' => 'submit', 'class' => 'btn-primary'],
]);

ActiveForm::end();


echo GridView::widget([
    'layout' => '{items}{pager}',
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'attribute' => 'Period',
            'label' => 'Период',
            'value' => function($data) {
                $time = strtotime($data['Period']);
                return (date('j', $time) <= 16 ? '01-16' : '17-'.date('t', $time))
                    . date('.m.Y', $time);
            }
        ],
        [
            'attribute' => "UserId",
            'label' => "Автор",
            'value' => function($data) {
                return User::getNameById($data['UserId']);
            }
        ],
        [
            'attribute' => 'ApproveCount',
            'label' => "Кол-во публикаций видео",
        ],
    ],
]);
Pjax::end();
