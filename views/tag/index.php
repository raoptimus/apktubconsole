<?php
/**
 * @var $this \yii\web\View
 * @var $dataProvider
 * @var $searchModel
 */
use yii\widgets\Pjax;
use yii\grid\GridView;
use app\models\video\Tag;
use app\components\MyHtml as Html;
?>
<div>
    <div class="well well-sm clearfix">
        <?php
        echo Html::a(
            Html::glyphicon("plus") . " " . Yii::t("dict", "Create"),
            ['/tag/create'],
            ['class' => 'btn btn-primary pull-right']
        );
        ?>
    </div>
    <div class="tag-list">
    <?php
    Pjax::begin(['options' => ['class' => 'pjax']]);
    echo GridView::widget([
        'layout' => '{items}{pager}',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'searchTitle' => [
                'attribute' => 'searchTitle',
                'value' => function($e) {
                    /**
                     * @var Tag $e
                     */
                    return $e->FormTitle;
                },
                'label' => 'Title'
            ],
            'VideoCount',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}&nbsp;&nbsp;{update}',
                'options' => ['width' => '50']
            ],
        ]
    ]);
    Pjax::end();
    ?>
    </div>
</div>