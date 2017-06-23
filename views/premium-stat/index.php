<?php

use app\models\premium\Stat;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use app\components\MyHtml as Html;

/**
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $model app\models\premium\Stat
 * */

?>
    <div class="well well-sm clearfix">
        <?php
        $form = ActiveForm::begin([
            'layout' => 'inline',
            'options' => ['data-pjax' => true, 'class' => 'well well-sm'],
//            'action' => Url::to([$this->context->action->id]),
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
            'pluginOptions' => [
                'locale' => ['format' => 'YYYY-MM-DD']
            ]
        ]);
        echo $form->field($model, 'Operation')->
            dropDownList(['all' => 'Все операции'] + $model->Operations, ['onchange' => 'this.form.submit()']);
        echo $form->field($model, 'Tariff')->
            dropDownList(['all' => 'Все тарифы'] + $model->Tariffs, ['onchange' => 'this.form.submit()']);

        ActiveForm::end();

        ?>
    </div>
<?php
Pjax::begin();

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'options' => [
        'class' => 'journal-list'
    ],
    'columns' => [
        [
            'attribute' => 'date',
            'label' => 'Дата',
            'value' => function ($v) {
                return date('Y-m-d', $v['date']);
            }
        ],
        [
            'attribute' => 'operationCount',
            'label' => 'Количество операций'
        ],
        [
            'attribute' => 'tariffId',
            'value' => function ($e) {
                return Stat::getTariffTitle($e['tariffId']);
            },
            'label' => 'Тариф'
        ],
        [
            'attribute' => 'price',
            'format' => 'raw',
            'value' => function ($e) {
                return '<b style="color:' . ($e['price'] < 0 ? 'red' : 'green') . '">' . $e['price'] . '</b>';
            },
            'label' => 'Сумма',
        ],
    ],
]);
Pjax::end();