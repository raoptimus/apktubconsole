<?php

use app\components\MyHtml as Html;
use app\models\push\Action;
use app\models\push\Repeat;
use app\models\push\State;
use app\models\push\Task;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\bootstrap\Nav;

/**
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $searchModel app\models\push\TaskSearch
 * */

Pjax::begin(['options' => ['class' => 'pjax']]);

echo Nav::widget([
    'options' => ['class' => 'nav-tabs'],
    'items' => [
        [
            'label' => Html::glyphicon("play", [ 'style' => 'color: green']) . ' Включенные',
            'url' => ['index', 'enabled' => 1, 'deleted' => 0],
            'active' => $searchModel->Enabled == 1 && $searchModel->Deleted == 0,
        ],
        [
            'label' => Html::glyphicon("stop", [ 'style' => 'color: red']) . ' Выключенные',
            'url' => ['index', 'enabled' => 0, 'deleted' => 0],
            'active' => $searchModel->Enabled == 0 && $searchModel->Deleted == 0,
        ],
        [
            'label' => Html::glyphicon("ban-circle") . ' Удалённые',
            'url' => ['index', 'enabled' => 0, 'deleted' => 1],
            'active' => $searchModel->Enabled == 0 && $searchModel->Deleted == 1,
        ],
    ],
    'encodeLabels' => false
]);


echo GridView::widget([
    'layout' => '{items}{pager}',
    'filterModel' => $searchModel,
    'dataProvider' => $dataProvider,
    'columns' => [
        '_id',
/*        'Enabled' => [
            'value' => function (Task $m) {
                return Html::glyphicon($m->Enabled ? "play" : "stop",
                    [
                        'style' => 'color: ' . ($m->Enabled ? "green" : "red"),
                    ]);
            },
            'attribute' => 'Enabled',
            'format' => 'html',
            'label' => 'Enabled',
            'filter'=> Html::activeDropDownList($searchModel, 'Enabled', ['all' => 'All', true => 'Enabled',false => 'Disabled'], ['class' => 'form-control']),
        ],*/
        'Note',
        'Push' => [
            'label' => 'Push',
            'value' => function (Task $m) {
                return
                    Html::beginTag("div", ["class" => "media", 'style' => 'width: 310px;']) .
                    Html::beginTag("div", ['class' => 'media-left']) .
                    Html::img(empty($m->IconFile) ? $m->IconUrlForm : Url::toRoute(['push-task/get-icon','id' => $m->IconFile]), ['class' => 'media-object', 'style' => 'height: 64px; width: 64px;']) .
                    Html::endTag("div") .
                    Html::beginTag("div", ['class' => 'media-body']) .
                    Html::tag("h4", $m->getTranslatedHeader(), ['class' => 'media-heading']) .
                    $m->getTranslatedMessage() .
                    Html::endTag("div") .
                    Html::endTag("div");
            },
            'format' => 'html',
        ],
        'PushSendedCount',
        'PushClickCount',
        'Repeat' => [
            'value' => function (Task $m) {
                return Repeat::getValue((int)$m->Repeat);
            },
            'attribute' => 'Repeat',
        ],
        'Action' => [
            'attribute' => 'Action',
            'value' => function (Task $m) {
                return Action::getValue($m->Action);
            },
            'format' => 'text',
        ],
        'State' => [
            'attribute' => 'State',
            'value' => function (Task $m) {
                return State::getValue($m->State);
            },
            'format' => 'text',
        ],
        'AddedDate' => [
            'attribute' => 'AddedDate',
            'value' => function (Task $m) {
                return date("Y-m-d", $m->AddedDate->sec);
            },
            'format' => 'text',
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{update}<br>{toggleEnabled}<br>{toggleDelete}',
            'buttons' => [
                'toggleEnabled' => function ($url, Task $model) {
                    if ($model->Deleted) {
                        return '';
                    }
                    return Html::a(
                        Html::glyphicon(($model->Enabled ? "stop" : "play")),
                        Url::toRoute([
                            'toggle-enabled',
                            'id' => $model->_id,
                            'enable' => !$model->Enabled,
                        ]),
                        [
                            'class' => 'btn btn-tooltip grid-action',
                            'title' => Yii::t("dict", ($model->Enabled ? "Disable" : "Enable")),
                            'data-placement' => 'left',
                            'data-pjax' => '0',
                        ]
                    );
                },
                'update' => function ($url, Task $model) {
                    return Html::a(
                        Html::glyphicon("pencil"),
                        $url,
                        [
                            'class' => 'btn btn-tooltip',
                            'title' => Yii::t("yii", "Update"),
                            'data-placement' => 'left',
                            'data-pjax' => '0',
                        ]
                    );
                },
                'toggleDelete' => function ($url, Task $model) {
                    return Html::a(
                        boolval($model->Deleted) ? Html::glyphicon("retweet") : Html::glyphicon("trash"),
                        Url::toRoute(['toggle-deleted', 'id' => $model->id]),
                        [
                            'class' => 'btn btn-tooltip grid-action',
                            'title' => Yii::t("yii", boolval($model->Deleted) ? "Restore" : "Delete"),
                            'data-placement' => 'left',
                            'data-pjax' => '0',
                        ]
                    );
                },
            ],
        ]
    ],
]);
Pjax::end();