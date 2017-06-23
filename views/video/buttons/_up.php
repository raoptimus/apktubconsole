<?php
/**
* @var $model \app\models\video\Video
*/
use app\components\MyHtml as HTML;
use yii\helpers\Url;

echo Html::a(
    Html::glyphicon("arrow-up"),
    ["up", "id" => $model->_id, "url" => Url::current()],
    ['class' => 'btn btn-default btn-tooltip grid-action',
        'title' => 'Поднять',
        'data-placement' => "bottom",
    ]
);