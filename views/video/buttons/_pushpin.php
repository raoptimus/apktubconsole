<?php
/**
 * @var $model \app\models\video\Video
 */
use app\components\MyHtml as HTML;
use yii\helpers\Url;

echo Html::a(
    Html::glyphicon("pushpin"),
    ["toggle-featured", "id" => $model->_id, "url" => Url::current()],
    ['class' => 'btn btn-default btn-tooltip grid-action ' . ($model->Featured() ? 'active' : ''),
        'title' => 'Показывать на индексе',
        'data-placement' => "bottom",
    ]
);