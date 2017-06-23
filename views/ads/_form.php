<?php

use app\components\MyHtml as Html;
use yii\widgets\ActiveForm;
use app\models\users\Device;
use app\models\Ages;
use app\models\Country;
use yii\helpers\Url;
use kartik\rating\StarRating;
use kartik\sortable\Sortable;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model app\models\Ads */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ads-form">

    <?php $form = ActiveForm::begin([
        'options' => ['enctype'=>'multipart/form-data']
    ]);

    echo $form->errorSummary($model);

    echo $this->render('_langForm', [
        'model' => $model,
        'form' => $form
    ]);

    echo $form->field($model, 'Note')->textarea();

    echo $form->field($model, 'Link')->textInput();

    echo $form->field($model, 'CarrierType')->checkboxList(Device::getCarrierTypes());

    ?>

	<?php
		$countriesList = $form->field($model, 'CountriesForm');
		echo $countriesList->begin();
		echo Html::activeLabel($model, 'CountriesForm');
	?>
    <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#importCountriesModal">
        <?=Yii::t('dict', 'import list')?>
    </button>

	<div id="countriesCheckboxes">
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
						<span class="toggle-region-label" data-toggle='collapse' aria-controls='collapseRegion<?=$region?>' data-target="#collapseRegion<?=$region?>">
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

	<?php
        if (Yii::$app->user->can('beAdmin')) {
            echo $form->field($model, 'Sort')->textInput(['maxlength' => 55]);
        }
    ?>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'Age')->dropDownList(Ages::getList());?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'Status')->dropDownList(['Running' => 'Running', 'Stopped' => 'Stopped']) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'Rating')->widget(StarRating::classname(), [
                'pluginOptions' => [
                    'step' => 0.5,
                    'showClear' => false,
                    'showCaption' => true,
                    'starCaptions' => [
                        '0' => '0',
                        '0.5' => '0.5',
                        '1' => '1',
                        '1.5' => '1.5',
                        '2' => '2',
                        '2.5' => '2.5',
                        '3' => '3',
                        '3.5' => '3.5',
                        '4' => '4',
                        '4.5' => '4.5',
                        '5' => '5',
                    ],
                ]
            ]); ?>
        </div>
    </div>

    <div class="row">
        <?php
        if (!empty($model->Icon)) { ?>
        <div class="col-md-4">
            <div class="form-group field-ads-iconform">
                <label class="control-label" for="ads-iconform">Current Icon</label>
                <div class="thumbnail text-center" style="width:240px;">
                    <img src= <?=Url::toRoute(['ads-icon/get-icon', 'id' => $model->Icon] )?> />
                </div>
                        <?php
                        $options = [
                            'title' => Yii::t('yii', 'Delete'),
                            'aria-label' => Yii::t('yii', 'Delete'),
                            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this icon?'),
//                            'data-method' => 'post',
//                            'data-pjax' => '0',
                            'class' => 'btn btn-warning'
                        ];
                        echo Html::a(Html::glyphicon('trash') . ' Удалить иконку', Url::toRoute(['delete-icon', 'model' => $model->id]), $options);    ?>
            </div>
        </div>
        <?php } else { ?>
        <div class="col-md-4">
                <?= $form->field($model, 'IconForm')->fileInput(); ?>
        </div>
        <?php } ?>
    </div>




    <?php
        if (count($model->Screenshots) > 0) {
            $widget_items = [];
            foreach ($model->Screenshots as $index => $shot) {
                $widget_items[] = [
                    'content' => '<div class="thumbnail text-center image-sorter" data-index="'.$index.'" data-id="'.$shot.'" style="width:200px;">'.
                        Html::img(Url::toRoute(['/ads-screen-shot/get-shot/', 'id' => $shot]) , ['class'=>'file-preview-image', 'style' => 'height: 160px; vertical-align: middle;']).
                        Html::button(
                            Html::glyphicon('trash', ['class' => 'text-danger']),
                            [
                                'class' => 'kv-file-remove btn btn-xs btn-default sorter-image-delete',
                                'title' => "Удалить фаил",
                                'style' => 'margin-top: 10px;',
                                'data-url' => Url::toRoute(['delete-icon','id' => $shot, 'model' => $model->id])
                            ]
                        ) .
                    '</div>'
                ];
            }

            echo Sortable::widget([
                'type'=>'grid',
                'items'=> $widget_items,
                'pluginEvents' => [
                    'sortupdate' => 'function() {
                        acc.sortable.sort("'.Url::toRoute(['sort-shots', 'id' => $model->id]).'")
                    }'
                ]
            ]);
        }

        echo $form->field($model, 'ScreenShotsForm[]')->fileInput(['multiple' => true, 'accept' => 'image/*'])->label();
    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('dict', 'Create') : Yii::t('dict', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?=$this->render('_importCountries', ['activeCountries' => $model->Countries])?>
</div>
