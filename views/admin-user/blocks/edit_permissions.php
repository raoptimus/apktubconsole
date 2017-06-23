<?php
/**
 * @var $dataProvider yii\data\ActiveDataProvider
 */
use app\components\MyHtml as Html;
use yii\helpers\Url;

echo yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'name',
        'description',
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{update} {remove}',
            'buttons' => [
                'update' => function($url, $item){
                    return Html::a(
                        Html::glyphicon('pencil'),
                        Url::toRoute(
                            [
                                'admin-user-roles/update-permission',
                                'id' => strval($item->_id)
                            ]
                        ),
                        [
                            'class'=>'btn action-column-icon btn-tooltip',
                            'title' => 'Редактировать Разрешение',
                            'data-placement' => 'top',
                        ]
                    );
                },
                'remove' => function($url, $item){
                    return Html::a(
                        Html::glyphicon('trash'),
                        Url::toRoute(
                            [
                                'admin-user-roles/remove',
                                'name' => strval($item->name)
                            ]
                        ),
                        [
                            'class'=>'btn action-column-icon btn-tooltip',
                            'title' => 'Удалить разрешение',
                            'data-placement' => 'top',
                            'data-confirm' => 'Вы на самом деле хотите удалить разрешение ' . $item->name . "?\n Эта операция необратимая!",
                        ]
                    );
                },
            ]
        ],
    ],
    'layout' => "{items}\n{pager}"
]); ?>
<p class="pull-right">
    <?= yii\helpers\Html::a(Yii::t('dict', 'Create new permission'), ['admin-user-roles/create-permission'], ['class' => 'btn btn-success']) ?>
</p>
