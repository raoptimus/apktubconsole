<?php
use app\components\MyHtml as Html;
use yii\helpers\Url;
?>
<div style="position: relative;" class="from-<?=$index % 2 == 0 ? 'me' : 'them' ?>">
    <div style="height: 30px; width:20px; position: absolute; right:0; top:20px;">
        <?=Html::glyphicon('remove-sign')?>
    </div>
    <p><?=$model->Body?></p>
    <small>Оставил пользователь <?=Html::a($model->UserIdTitle, Url::toRoute(['appuser/view', 'id' => $model->UserId]))?> к видео <?=Html::a($model->VideoIdTitle,Url::toRoute(['video/edit','id'=>$model->VideoId]))?></small>
</div>
<div class="sms-clear"></div>
