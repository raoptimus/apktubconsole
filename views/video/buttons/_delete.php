<?php
/**
* @var $model \app\models\video\Video
*/
use app\components\MyHtml as HTML;
use yii\helpers\Url;

echo Html::a(
    Html::glyphicon("trash"),
    ["delete", "id" => $model->_id, "url" => Url::current()],
    [
        'class' => 'btn btn-default btn-tooltip delete-confirm',
        'title' => 'Удалить',
        'data-placement' => "bottom",
        'data-action' => 'post',
    ]
);