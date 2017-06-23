<?php
use app\components\MyHtml as Html;
use yii\helpers\Url;

echo yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'username',
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{update} {delete} {block}',
            'buttons' => [
                'delete' => function($url, $item){
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
                            'title' => 'Удалить пользователя',
                            'data-placement' => 'top',
                            'data-confirm' => 'Вы на самом деле хотите удалить пользователя ' . $item->username . "?\n Эта операция необратимая!",
                        ]
                    );
                },
                'block' => function($url, $item){
                    $action = $item->Blocked == 1 ? 'Разблокировать': 'Заблокировать';
                    return Html::a(
                        Html::glyphicon($item->Blocked == 1 ? 'eye-open':'eye-close'),
                        Url::toRoute(
                            [
                                'toggle-block',
                                'id' => strval($item->_id)
                            ]
                        ),
                        [
                            'class'=>'btn action-column-icon btn-tooltip',
                            'title' => $action . ' пользователя',
                            'data-placement' => 'top',
                            'data-confirm' => 'Вы на самом деле хотите '. $action .' пользователя ' . $item->username . "?",
                        ]
                    );
                },
            ]

        ],
    ],
    'layout' => "{items}\n{pager}"
]); ?>
<p class="pull-right">
    <?= yii\helpers\Html::a(Yii::t('dict', 'Create Admin User'), ['create'], ['class' => 'btn btn-success']) ?>
</p>
