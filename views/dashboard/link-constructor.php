<?php
/* @var $this yii\web\View */
?>
<div>
    <form>
        <div class="form-group">
            <label for="landingURL">Ссылка на лендинг</label>
            <input type="text" class="form-control" id="landingURL" readonly ">
        </div>
        <div class="form-group">
            <label for="apkURL">Ссылка на APK</label>
            <input type="text" class="form-control" id="apkURL" readonly ">
        </div>

        <div class="form-group">
            <label for="u">Аккаунт партнёра</label>
            <input type="text" class="form-control" id="u" readonly value="<?=Yii::$app->user->getIdentity()->username?>">
        </div>
        <div class="form-group">
            <label for="s">source/site источник</label>
            <input type="text" class="form-control userInput" id="s" >
        </div>
        <div class="form-group">
            <label for="a">Идентификатор баннера</label>
            <input type="text" class="form-control userInput" id="a" >
        </div>
        <div class="form-group">
            <label for="_t">TRANSACTION_ID</label>
            <input type="text" class="form-control userInput" id="_t" >
        </div>
        <div class="form-group">
            <label for="_a">AFFILIATE_ID</label>
            <input type="text" class="form-control userInput" id="_a" >
        </div>
        <div class="form-group">
            <label for="_o">OFFER_ID</label>
            <input type="text" class="form-control userInput" id="_o"  >
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" id="_af" class="userInput"> <b>включить аппфлайер</b>
            </label>
        </div>
    </form>
</div>

<?php $this->registerJsFile("/js/constructor.js", ['depends' => 'yii\web\JqueryAsset']); ?>