<?php

use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use app\components\MyHtml as Html;
use app\models\JournalSearch;
use app\models\User;
use app\models\Priority;
use kartik\daterange\DateRangePicker;
/**
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $searchModel app\models\JournalSearch
 * */

Pjax::begin();

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'options' => [
        'class' => 'journal-list'
    ],
    'columns' => [
        [
            'attribute' => 'Priority',
            'format' => 'raw',
            'value'=>function ($data) {
                return Priority::getIconValue($data->Priority);
            },
            'options' => [
                'width' => 150,
                'class' => 'log-level',
            ],
            'filter'=> Html::activeDropDownList($searchModel, 'Priority', array_merge(['all' => 'Все'],Priority::getList()), ['class' => 'form-control']),
        ],
        [
            'attribute' => 'Time',
            'value'=>function ($data) {
                return date('Y-m-d H:i:s',$data->ReadableTime);
            },
            'filter' => DateRangePicker::widget([
                'name'=>'date_range',
                'value'=> date('Y-m-d', $searchModel->DateFrom) . ' - ' . date('Y-m-d',$searchModel->DateTo),
                'presetDropdown'=>true,
                'pluginOptions'=>[
                    'locale'=>['format'=>'YYYY-MM-DD']
                ]
            ]),
            'options' => [
                'width' => 210,
                'class' => 'log-time'
            ],
        ],
        [
            'attribute' => 'Hostname',
            'options' => [
                'width' => 250,
                'class' => 'log-host'
            ],
        ],
        [
            'attribute' => 'Tag',
            'options' => [
                'class' => 'log-tag'
            ],
        ],
        [
            'attribute' => 'Msg',
            'value'=>function ($data) {
                return print_r($data->Msg, true);
            },
            'format' => 'raw',
            'options' => [
                'class' => 'log-msg'
            ],
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{jira}',
            'buttons' => [
                'jira' => function($url, $item){
                    return Html::a(
                        Html::glyphicon('send'),
                        '#',
                        [
                            'class'=>'btn action-column-icon btn-tooltip jira-link',
                            'title' => 'Отправить в Jira',
                            'data-placement' => 'left',
                        ]
                    );
                },
            ]
        ],    ],
]);
Pjax::end();
?>
<a href="" id="hidden-link" style="display: none" target="_blank">123</a>
