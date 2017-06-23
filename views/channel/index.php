<?php
/**
 * @var $this \yii\web\View
 * @var $dataProvider
 * @var $searchModel
 */
use yii\widgets\Pjax;
use yii\grid\GridView;
use app\components\MyHtml as Html;
use yii\helpers\Url;
?>
<div>
    <div class="well well-sm clearfix">
        <?php
        echo Html::a(
            Html::glyphicon("plus") . " " . Yii::t("dict", "Create"),
            ['/channel/create'],
            ['class' => 'btn btn-primary pull-right']
        );
        ?>
    </div>
    <?php
    Pjax::begin(['options' => ['class' => 'pjax']]);
    echo GridView::widget([
        'layout' => '{items}{pager}',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            '_id',
            'Title',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}&nbsp;&nbsp;{remove}',
                'buttons' => [
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
                                'title' => 'Удалить канал',
                                'data-placement' => 'left',
                                'data-confirm' => 'Вы на самом деле хотите удалить канал?',
                            ]
                        );
                    },
                ]
            ],
        ]
    ]);
    Pjax::end();
    ?>
</div>