<?php

use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use app\components\MyHtml as Html;
use app\models\JournalSearch;
use app\models\User;
use app\models\Currency;

/**
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $searchModel app\models\JournalSearch
 * */

?>
    <div class="well well-sm clearfix">
        <?php
        echo Html::a(
            Html::glyphicon("plus") . " " . Yii::t("dict", "Create"),
            ['create'],
            ['class' => 'btn btn-primary pull-right']
        );
        ?>
    </div>
<?php
Pjax::begin();

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'options' => [
        'class' => 'journal-list'
    ],
    'columns' => [
        [
            'attribute' => 'Title',
            'value' => function ($e) {
                return $e->FormTitle;
            },
            'label' => 'Title'
        ],
        "DisplayPrice",
        [
            'attribute' => 'Time',
            'format' => 'raw',
            'value' => function ($data) {
                return $data->TimeFormat;
            },
        ],
        [
            'attribute' => "Currency",
            'value' => function ($model) {
                return Currency::getValue($model->Currency);
            },
            'filter' => Html::activeDropDownList($searchModel, 'Currency',
                array_merge(['all' => 'Все'], Currency::getList()),
                ['class' => 'form-control']),

        ],
        [
            'attribute' => "Enabled",
            'value' => function ($model) {
                return $model->Enabled ? 'Вкл' : 'Выкл';
            },
            'filter' => Html::activeDropDownList($searchModel, 'Enabled',
                ['all' => 'Все', true => 'Вкл', false => 'Выкл'],
                ['class' => 'form-control']),

        ],
        [
            'attribute' => 'CreationDate',
            'format' => 'raw',
            'value' => function ($data) {
                return date('Y-m-d H:i:s', $data->CreationDateFormat);
            },
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{update}&nbsp;&nbsp;{delete}',
        ],
    ],
]);
Pjax::end();