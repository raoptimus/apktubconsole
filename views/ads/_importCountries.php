<?php
use app\components\MyHtml as Html;
?>

<div class="modal fade" id="importCountriesModal" tabindex="-1" role="dialog" aria-labelledby="importCountriesLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="importCountriesLabel"><?=Yii::t('dict', 'CountriesFormCodes')?></h4>
            </div>
            <div class="modal-body">
                <?=Html::textarea('CountriesFormCodes', join(', ', $activeCountries ?: []), ['class' => "form-control", 'rows' => 5])?>
                <div class="help-block"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" data-save="true">Save</button>
            </div>
        </div>
    </div>
</div>
