<?php

use app\components\MyHtml as Html;
use yii\bootstrap\Nav;
use yii\grid\GridView;
use yii\helpers\Url;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;
use yii\web\View;
use app\components\TotalRow;

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
echo $form->field($model, "By")->hiddenInput();
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

if (Yii::$app->user->can('beManager')) {
    echo($form->field($model,'Apk')->dropDownList(['all' => 'Все приложения'] + $model->ApkList,['onchange'=>'this.form.submit()']));
    echo $form->field($model,'Ver')->dropDownList(['all' => 'Все версии']+$model->VerList,['onchange'=>'this.form.submit()']);
}
echo $form->field($model,'Site')->dropDownList(['all' => 'Все сайты']+$model->SiteList,['onchange'=>'this.form.submit()','id' => 'combobox']);
if (Yii::$app->user->can('beManager')) {
    echo $form->field($model, 'Landing')->dropDownList(['all' => 'Все лендинги'] + $model->LandingList, ['onchange' => 'this.form.submit()']);
    echo $form->field($model, 'Ad')->dropDownList(['all' => 'Все рекламы'] + $model->AdList, ['onchange' => 'this.form.submit()']);
    echo $form->field($model, 'Partner')->dropDownList(['all' => 'Все партнёры'] + $model->PartnerList, ['onchange' => 'this.form.submit()']);
}

ActiveForm::end();
$urlParams = [];
foreach ($model->attributes() as $attr) {
    if ($attr == "By") {
        continue;
    }
    $urlParams["f[{$attr}]"] = $model->{$attr};
}

$getParams = Yii::$app->request->get();
if (isset($getParams['f']) && isset($getParams['f']['DateRange'])) {
    $urlParams["f[DateRange]"] = Yii::$app->request->get()['f']['DateRange'];
}

echo Nav::widget([
    'options' => ['class' => 'nav-tabs'],
    'items' => [
        [
            'label' => Html::glyphicon("calendar") . ' По дате',
            'url' => [$this->context->action->id, 'f[By]' => 'Date'] +  $urlParams,
            'active' => $model->By == 'Date',
        ],
        [
            'label' => Html::glyphicon("iphone") . ' По apk',
            'url' => [$this->context->action->id, 'f[By]' => 'Apk'] + $urlParams,
            'active' => $model->By == 'Apk',
            'visible' => Yii::$app->user->can('beManager')
        ],
        [
            'label' => Html::glyphicon("history") . ' По версиям apk',
            'url' => [$this->context->action->id, 'f[By]' => 'Ver'] + $urlParams,
            'active' => $model->By == 'Ver',
            'visible' => Yii::$app->user->can('beManager')
        ],
        [
            'label' => Html::glyphicon("globe") . ' По сайту',
            'url' => [$this->context->action->id, 'f[By]' => 'Site'] + $urlParams,
            'active' => $model->By == 'Site',
        ],
        [
            'label' => Html::glyphicon("pushpin") . ' По лендингу',
            'url' => [$this->context->action->id, 'f[By]' => 'Landing'] + $urlParams,
            'active' => $model->By == 'Landing',
            'visible' => Yii::$app->user->can('beManager')
        ],
        [
            'label' => Html::glyphicon("bullhorn") . ' По рекламе',
            'url' => [$this->context->action->id, 'f[By]' => 'Ad'] + $urlParams,
            'active' => $model->By == 'Ad',
            'visible' => Yii::$app->user->can('beManager')
        ],
        [
            'label' => Html::glyphicon("user") . ' По партнёрам',
            'url' => [$this->context->action->id, 'f[By]' => 'Partner'] + $urlParams,
            'active' => $model->By == 'Partner',
            'visible' => Yii::$app->user->can('beManager')
        ]
    ],
    'encodeLabels' => false
]);

$fields = $model->visibleAttributes(Yii::$app->user->can('beManager'));
$labels = $model->attributeLabels();
$cleanFields = [];
foreach ($fields as $field) {
    $cleanFields[] = [
        'attribute' => $field,
        'footer'=> '<b>' . TotalRow::pageTotal($dataProvider->models,$field) . '</b>',
        'label' => $labels[$field]
    ];
}

echo GridView::widget([
    'layout' => '{items}{pager}',
    'dataProvider' => $dataProvider,
    'showFooter' => true,
    'columns' => array_merge([
        [
            'label' => Yii::t('dict',$model->getAttributeLabel($model->By)),
            'value' => function ($model) {
                return $model['_id'];
            },
            'options' => ['class' => 'nowrap'],
            'footer' => '<b>' . Yii::t('dict','Total') . '</b>'
        ],
    ], $cleanFields),

]);
Pjax::end();

$this->registerCssFile('/css/bootstrap-combobox.css');
$this->registerJsFile('/js/bootstrap-combobox.js',['depends' => 'yii\bootstrap\BootstrapPluginAsset']);
$script = '$(document).ready(function(){$("#combobox").combobox();});';
$this->registerJs($script,View::POS_END);