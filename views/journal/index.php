<?php

use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use app\components\MyHtml as Html;
use app\models\JournalSearch;
use app\models\User;
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
            'attribute' => '_id',
            'format' => 'raw',
            'value'=>function ($data) {
                return Html::a(substr($data->id, 0,10) . '...',Url::toRoute(['view', 'id' => $data->id]));
            },
        ],
        [
            'attribute' => "UserId",
            'value' => function($model) {
                return User::getNameById($model->UserId);
            }
        ],
        "UserIp",
        "Operation",
        "ObjectId",
        "ObjectName",
//        "Details",
        "AddedDate.sec:datetime",
    ],
]);
Pjax::end();