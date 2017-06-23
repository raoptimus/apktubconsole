<?php

use yii\grid\GridView;
use app\models\Language;
use yii\helpers\Url;
use app\components\MyHtml as Html;
use app\models\CommentStatus;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ApplicationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        '_id',
        [
            'attribute' => 'VideoId',
            'label' => Yii::t('dict','Video'),
            'format' => 'raw',
            'value' => function ($model) {return Html::a($model->VideoIdTitle, Url::toRoute(['video/edit', 'id' => $model->VideoId]));}
        ],
        [
            'attribute' => 'UserId',
            'label' => Yii::t('dict','User'),
            'format' => 'raw',
            'value' => function ($model) {return Html::a($model->UserIdTitle, Url::toRoute(['appuser/view', 'id' => $model->UserId]));}
        ],
        'Body',
        [
            'attribute' => 'Status',
            'value' => function ($model) {return Yii::t('dict', CommentStatus::getValue($model->Status));},
            'filter'=> Html::activeDropDownList($searchModel, 'Status', array_map(function($e) {return Yii::t('dict',$e);},array_merge([''=>'All'],CommentStatus::getList())), ['class' => 'form-control']),
        ],
        [
            'attribute' => 'PostDate',
            'value' => function ($model) {return date('Y-m-d', $model->PostDate->sec);}
        ],
        [
            'attribute' => 'Language',
            'value' => function ($model) {return Yii::t('dict', Language::getValue($model->Language));},
            'filter'=> Html::activeDropDownList($searchModel, 'Language', array_map(function($e) {return Yii::t('dict',$e);},array_merge([''=>'All'],Language::getList())), ['class' => 'form-control']),
        ],
        [
            'class' => 'yii\grid\ActionColumn',
//            'template' => '{spam}{ban}{remove}',
            'template' => '{spam}{remove}',
            'buttons' => [
                'spam' => function($url, $item){
                    return Html::a(
                        Html::glyphicon('ban-circle'),
                        Url::toRoute(
                            [
                                'spam',
                                'id' => strval($item->_id)
                            ]
                        ),
                        [
                            'class'=>'btn action-column-icon btn-tooltip',
                            'title' => 'Пометить как спам',
                            'data-placement' => 'left',
                        ]
                    );
                },
                'ban' => function($url, $item){
                    return Html::a(
                        Html::glyphicon('thumbs-down'),
                        Url::toRoute(
                            [
                                'appuser/ban',
                                'id' => strval($item->UserId)
                            ]
                        ),
                        [
                            'class'=>'btn action-column-icon btn-tooltip',
                            'title' => 'Забанить пользователя',
                            'data-placement' => 'left',
                        ]
                    );
                },
                'remove' => function($url, $item){
                    return Html::a(
                        Html::glyphicon('trash'),
                        Url::toRoute(
                            [
                                'remove',
                                'id' => strval($item->_id)
                            ]
                        ),
                        [
                            'class'=>'btn action-column-icon btn-tooltip',
                            'title' => 'Удалить коммент',
                            'data-placement' => 'left',
                        ]
                    );
                },
            ],
            'options' => ['class' => 'application-action-column text-center']
        ],
    ],
]);