<?php
/**
 * @var $model \app\models\video\Video
 */
use app\components\MyHtml as HTML;
use yii\bootstrap\Modal;
use app\components\selectPosterWidget;

Modal::begin ( [
    'id' => 'show_video_thumbs_' . $model->_id,
    'header' => 'Выбрать картинку',
    'footer' => HTML::button('Закрыть',['class' => 'btn btn-default', 'data-dismiss'  =>'modal']),
    'toggleButton' => [
        'tag' => 'button',
        'class' => 'btn btn-default btn-tooltip',
        'label' => Html::glyphicon("picture"),
    ],
    'options' => [
        'class' => '...apx_picture_modal'
    ],
] );

echo selectPosterWidget::widget(['pictures' => $model->getAllThumbs(), 'id' => $model->_id]);

Modal::end ();

$script = '$("#show_video_thumbs_' . $model->_id . '").on("shown.bs.modal", function (e) {
                    $("#image-picker-' . $model->_id . '").imagepicker();
                    $("#image-picker-' . $model->_id . '").on("change",function(){
                        saveVideoPoster(' . $model->_id . ',"'.Yii::$app->params['project'].'/");
                    });
                });';
$this->registerJs($script, yii\web\View::POS_READY);