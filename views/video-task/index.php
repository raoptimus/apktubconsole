<?php

use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\bootstrap\Nav;
use yii\bootstrap\Modal;
use yii\bootstrap\Button;
use app\models\users\AppUser;
use app\components\MyHtml as Html;
use app\models\Language;

/**
 * @var $this yii\web\View
 * @var $model app\models\users\Device
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $searchModel app\models\users\DeviceSearch
 * */

Pjax::begin(["options" => ["class" => "pjax"]]);

function tabLabel($icon, $label) {
    return Html::glyphicon($icon) . " " . Yii::t("dict", $label);
}

function tab($state, $icon, $label) {
    return [
        "label" => tabLabel($icon, $label),
        "url" => ["index", "state" => $state],
        "active" =>  ($state == Yii::$app->view->context->actionParams["state"]),
    ];
}

echo Nav::widget([
    "options" => ["class" => "nav-tabs"],
    "encodeLabels" => false,
    "items" => [
        tab("", "asterisk", "On manager"),
        tab("receive", "download", "Downloading"),
        tab("in-progress", "play", "In progress"),
        tab("wait", "pause", "Waiting"),
        tab("err", "remove-circle", "Erroneous"),
        tab("done", "check", "Done"),
    ],
]);

echo GridView::widget([
    "dataProvider" => $tasks,
    "options" => [
        "class" => "col-sm-9"
    ],
    "columns" => [
        "Id",
        "State",
        [
            "attribute" => "Status",
            "value" => function($m) {
                $rawstatus = $m["Status"];
                $status = json_decode($rawstatus);
                return ($status) ? $status->Progress . "%" : $rawstatus;
            },
        ],
        "Name",
        "Server",
        "Pid",
        "Started",
        [
            "label" => "Projects",
            "value" => function ($m) {
                return join(", ", $m["FormData"]["Projects"]);
            },
        ],
        [
            "class" => "yii\grid\ActionColumn",
            "template" => "{retry}<br>{kill}<br>{restart}",
            "buttons" => [
                "retry" => function($url, $m, $key) {
                    return Html::a("Retry", $url, ["class" => "grid-action"]);
                },
                "kill" => function($url, $m, $key) {
                    return Html::a("Kill", $url, ["class" => "grid-action"]);
                },
                "restart" => function($url, $m, $key) {
                    return Html::a("Restart", $url, ["class" => "grid-action"]);
                }
            ]
        ],
    ],
    "rowOptions" => function ($model, $key, $index, $grid) {
        return [
            "data-model" => json_encode($model, JSON_PRETTY_PRINT),
            "onclick" => "$('#task-info').html(this.dataset['model']);"
        ];
    },
]);
echo Html::tag("pre", "", [
    "id" => "task-info",
    "options" => ["class" => "col-sm-3", "style" => "height: 100%"]
]);
Pjax::end();
