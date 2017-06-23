<?php
/**
 * @var $model \app\models\video\Video
 */
use app\components\MyHtml as HTML;
use yii\bootstrap\Modal;

Modal::begin ( [
    'header' => $model->getLangTitle('ru'),
    'id' => 'video_player_modal_' . $model->_id,
    'toggleButton' => [
        'tag' => 'button',
        'class' => 'btn btn-default btn-tooltip',
        'label' => Html::glyphicon("play"),
    ]
] );

$sm = new \app\models\video\VideoFilter();
$sm->Language = 'ru';
echo $this->render('../_play', [
    'model' => $model
]);
Modal::end ();


$script = '$("#video_player_modal_' . $model->_id . '").on("hidden.bs.modal", function (e) {
    $("#video_player_' . $model->_id . '").remove();
    $("#video_player_modal_11730 .modal-body .embed-responsive").prepend("<div id=\'video_player_' . $model->_id . '\' class=\'embed-responsive-item\'></div>");
});';
$this->registerJs($script, yii\web\View::POS_READY);
