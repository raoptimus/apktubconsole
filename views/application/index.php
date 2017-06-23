<?php

use yii\grid\GridView;
use yii\helpers\Url;
use app\components\MyHtml as Html;
use yii\bootstrap\Nav;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ApplicationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

echo Nav::widget([
    'options' => ['class' => 'nav-tabs'],
    'items' => [
        [
            'label' => Html::glyphicon("ok-circle") . ' Релиз',
            'url' => ['application/index', 'status' => 1],
            'active' => $searchModel->Status == 1,
        ],
        [
            'label' => Html::glyphicon("ban-circle") . ' На тестировании',
            'url' => ['application/index', 'status' => 0],
            'active' => $searchModel->Status == 0,
        ],
    ],
    'encodeLabels' => false
]);

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'layout' => "{items}\n{pager}",
    'columns' => [
        'Name',
        'Ver',
        'BuildVer',
        [
            'attribute' => 'Description',
            'format' => 'raw',
            'value' => function($data) {
                return '<ul><li>' . implode('</li><li>',array_filter(array_map(function($e){return(trim($e));},explode('*',$data->Description)))) . '</li></ul>';
            }
        ],
        [
            'attribute' => 'AddedDate',
            'value' => function ($model) {
                return empty($model->AddedDate) ? '' : date('Y-m-d',$model->AddedDate->sec);
            }
        ],
        [
            'attribute' => 'ReleaseDate',
            'visible' => $searchModel->Status == 1,
            'value' => function ($model) {
                return empty($model->ReleaseDate) ? '' : date('Y-m-d',$model->ReleaseDate->sec);
            }
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => $searchModel->Status == 0 ? '{release}{push}{download}{remove}{update}' : '{push}{download}',
            'buttons' => [
                'release' => function($url, $item){
                    return Html::a(
                        Html::glyphicon('check'),
                        Url::toRoute(
                            [
                                'release',
                                'id' => strval($item->_id)
                            ]
                        ),
                        [
                            'class'=>'btn action-column-icon btn-tooltip',
                            'title' => 'Выпустить',
                            'data-placement' => 'left',
                        ]
                    );
                },
                'push' => function($url, $item){
                    return Html::a(
                        Html::glyphicon('send'),
                        Url::toRoute(['push-task/create', 'Action' => 1, 'GoUrl' => $item->Name . '://']),
//                        "/push-task/create&Action=1&GoUrl={$item->Name}://",
                        [
                            'class'=>'btn action-column-icon btn-tooltip',
                            'title' => 'Создать пуш',
                            'data-placement' => 'left',
                        ]
                    );
                },
                'update' => function($url, $item){
                    return Html::a(
                        Html::glyphicon('pencil'), Url::toRoute(['update', 'id' => $item->id]),
                        [
                            'class'=>'btn action-column-icon btn-tooltip',
                            'title' => 'Редактировать',
                            'data-placement' => 'left',
                        ]
                    );
                },
                'download' => function($url, $item){
                    return Html::a(
                        Html::glyphicon('save'),
                        Url::toRoute(
                            [
                                'download',
                                'id' => strval($item->_id)
                            ]
                        ),
                        [
                            'class'=>'btn action-column-icon btn-tooltip',
                            'title' => 'Скачать',
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
                            'title' => 'Удалить приложение',
                            'data-placement' => 'left',
                            'data-confirm' => 'Вы на самом деле хотите удалить приложение версии ' . $item->Ver . ' Билд номер ' . $item->BuildVer . "?\n Эта операция необратимая!",
                        ]
                    );
                },
            ],
            'options' => ['class' => 'application-action-column text-center']
        ],
    ],
]);