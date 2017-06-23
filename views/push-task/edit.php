<?php
/**
 * @var $this \yii\web\View
 * @var $model app\models\push\Task
 */

use app\components\MyHtml as Html;
use app\models\Language;
use app\models\push\Action;
use app\models\push\Repeat;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Button;
use yii\bootstrap\Tabs;
use yii\widgets\Pjax;
use kartik\file\FileInput;
use yii\helpers\Url;
use yii\web\View;
use app\models\users\Device;
use app\models\Country;

$form = ActiveForm::begin([
//        'enableAjaxValidation' => true,
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
    'options' => ['enctype'=>'multipart/form-data']
]);

echo $form->errorSummary($model);

echo Tabs::widget([
    'encodeLabels' => false,
    'items' => array_merge(
        array_map(function ($lng) use ($model, $form) {
            return [
                'label' => Yii::t('dict',Language::getValue($lng)),
                'content' => '<br>' .
                    $form->field($model, "HeaderForm[{$lng}]")->textInput() .
                    $form->field($model, "MessageForm[{$lng}]")->textarea()
                ,
            ];

        }, array_keys($model->getMessageForm())),
        [
            [
                'label' => Html::glyphicon("plus"),
                'items' => array_map(function ($lng) use ($model, $form) {
                    return [
                        'label' => Yii::t('dict',Language::getValue($lng)),
                        'options' => ['class' => 'nav-tab-lang'],
                        'content' => '<br>' .
                            $form->field($model, "HeaderForm[{$lng}]")->textInput() .
                            $form->field($model, "MessageForm[{$lng}]")->textarea()
                        ,
                    ];
                }, array_diff(Language::getKeys(), array_keys($model->getMessageForm()))),
            ]
        ]

    ),
]);

echo $form->field($model, "Note")->textInput();

echo $form->field($model, "IconUrlForm")->textInput(['type' => 'url'])->hint($model->getAttributeHint("IconUrl"));

$fileOptions = [
    'options' => [
        'accept' => 'image/*',
        'id' => 'task-icon-uploader'
    ],
    'pluginOptions' => [
        'showRemove' => false,
        'showUpload' => false,
    ],
];

if (!empty($model->IconFile)) {
    $fileOptions['pluginOptions']['initialPreview'] = [
        Html::img(Url::toRoute(["/push-task/get-icon/", 'id' => $model->IconFile]), ['class'=>'icon-file-preview-image']),
    ];
    $fileOptions['pluginOptions']['showRemove'] = true;
}

echo $form->field($model, 'IconFileForm')->widget(FileInput::classname(), $fileOptions);
echo $form->field($model, "GoUrl")->textInput(['type' => 'url'])->hint($model->getAttributeHint("GoUrl"));
echo $form->field($model, "FrequencyHours")->textInput(['type' => 'number'])->hint($model->getAttributeHint("FrequencyHours"));
echo $form->field($model, "Hour")->dropDownList(range(0, 23))->hint($model->getAttributeHint("Hour"));
echo $form->field($model, "MaxHour")->dropDownList(range(0, 23))->hint($model->getAttributeHint("MaxHour"));

echo $form->field($model, 'CarrierType')->checkboxList(Device::getCarrierTypes());

?>

<?php
    $countriesList = $form->field($model, 'CountriesForm');
    echo $countriesList->begin();
    echo Html::activeLabel($model, 'CountriesForm', ['class' => 'control-label col-sm-2']);
?>

<div class="col-sm-8" id="countriesCheckboxes">
    <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#importCountriesModal">
        <?=Yii::t('dict', 'import list')?>
    </button>
    <?php
    foreach (Country::getCountiesList() as $region => $regionsList) {
        if (is_array($regionsList)) {
            $aSelectedCountriesInThisRegion = array_intersect_key($regionsList['items'], $model->CountriesForm);
            $isRegionSelected = !empty($aSelectedCountriesInThisRegion);
            $isFullRegionSelected = (count($aSelectedCountriesInThisRegion) == count($regionsList['items']));
            ?>
            <div>
                <span>
                    <?=Html::checkbox(NULL, $isRegionSelected, ['uncheck' => null, 'data-region-checkbox' => $region]) ?>
                    <span data-toggle='collapse' aria-controls='collapseRegion<?=$region?>' data-target="#collapseRegion<?=$region?>">
                        <?=$regionsList['title']?>
                        <b class="caret"> </b>
                    </span>
                </span>
                <div class="collapse <?=($isRegionSelected and !$isFullRegionSelected) ? 'in' : '' ?>" id="collapseRegion<?=$region?>" style="margin-left: 2em">
                <?php foreach ($regionsList['items'] as $countryKey => $countryLabel) {?>
                    <div>
                        <?=Html::activeCheckbox($model, 'CountriesForm[' . $countryKey . ']', ['label' => $countryLabel, 'data-country' => $countryKey])?>
                    </div>
                <?php } ?>
                </div>
            </div>
        <?php } else { ?>
            <div>
                <?=Html::activeCheckbox($model, 'CountriesForm[' . $region . ']', ['label' => $regionsList, 'data-country' => $region])?>
            </div>
        <?php
        }
    } ?>
    <div>
        <?=Html::activeCheckbox($model, 'CountriesForm['.Country::UNKNOWN.']', ['label' => "Остальные", 'data-country' => Country::UNKNOWN])?>
    </div>

    <?=$countriesList->end()?>
</div>

<?=$this->render('//ads/_importCountries', ['activeCountries' => $model->Countries])?>

<?php

//hours since till
//echo Html::beginTag("div", ["class" => "form-group"]);
//echo Html::label(Yii::t("dict", "Hour"), null, ['class' => 'control-label col-sm-2']);
//echo Html::beginTag("div", ["class" => "col-sm-8"]);
//echo Html::beginTag("div", ["class" => "form-inline"]);
//$form->layout = "inline";
//echo $form->field($model, "HourSince")->dropDownList(range(0, 23));
//echo $form->field($model, "HourTill")->dropDownList(range(0, 23));
//echo Html::endTag("div");
//echo Html::endTag("div");
//echo Html::endTag("div");
//$form->layout = "horizontal";
//<-

echo $form->field($model, "Repeat")->dropDownList(Repeat::getListTranslated());

if ($model->isNewRecord) {
    echo $form->field($model, "Enabled")->checkbox()->hint($model->getAttributeHint("Enabled"));
    echo $form->field($model, "ActionForm")->dropDownList(Action::getListTranslated(), [
        'onchange' => '$.pjax.reload("#" + $(".pjax").prop("id"), {replace: false, data: {ActionForm: $(this).val()}});',
    ]);
} else {
    echo Html::beginTag("div", ["class" => "form-group"]);
    echo Html::activeLabel($model, "Action", ['class' => 'control-label col-sm-2']);
    echo Html::beginTag("div", ["class" => "col-sm-8"]);
    echo Html::tag("p", Action::getValueTranslated($model->Action), ['class' => 'form-control-static']);
    echo Html::endTag("div");
    echo Html::endTag("div");
}

Pjax::begin(['options' => ['class' => 'pjax']]);
?>
    <div>
        <?php
        /**
         * @var \app\components\MongoActiveRecord $actionModel
         */
        $actionModel = $model->getActionModel();
        foreach (array_keys($model->Options) as $k) {
            //todo type input
            echo $form->field($actionModel, $k, ['inputOptions' => ['name' => "Options[{$k}]"]])->
                hint($actionModel->getAttributeHint($k));
        }

        ?>
    </div>
<?php
Pjax::end();
?>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-8">
            <?php
            echo Button::widget([
                'label' => Yii::t('dict', $model->isNewRecord ? 'Create' : 'Save'),
                'options' => ['type' => 'submit', 'class' => 'btn-primary'],
            ]);
            ?>
        </div>
    </div>
<?php
ActiveForm::end();
$script = "
    $('#task-icon-uploader').on('fileclear', function(event) {
        $.get('".Url::toRoute(['/push-task/remove-icon','id' => $model->_id])."', function( data ) {
          alert( \"Иконка была удалена\" );
        });
    });
";
$this->registerJs($script, View::POS_READY);
