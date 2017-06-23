<?php
/**
 * @var $model \app\models\video\Video
 */
use app\components\MyHtml as HTML;
use yii\helpers\Url;

if (in_array('!approved', $model->Filters)) {
    echo Html::a(
        Html::glyphicon("thumbs-up"),
        ["approve", "id" => $model->_id, "url" => Url::current()],
        [
            'class' => 'btn btn-default btn-tooltip grid-action',
            'title' => 'Одобрить',
            'data-placement' => "bottom",
            'data-action' => 'post',
        ]
    );
}
