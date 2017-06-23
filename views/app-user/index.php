<?php

use app\models\users\PremiumTypeEnum;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use app\models\users\AppUser;
use app\components\MyHtml as Html;
use app\models\Language;

/**
 * @var $this yii\web\View
 * @var $model app\models\users\AppUser
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $searchModel app\models\users\AppUserSearch
 * */

Pjax::begin();

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        '_id' => [
            'attribute' => '_id',
            'format' => 'raw',
            'value' => function ($data) {
                return Html::a($data->_id, Url::toRoute(['view', 'id' => $data->_id]));
            },
        ],
        'UserName' => [
            'attribute' => 'UserName',
            'format' => 'raw',
            'value' => function ($data) {
                $name = empty($data->UserName) ? 'Guest' . $data->_id : $data->UserName;
                return Html::a($name, Url::toRoute(['view', 'id' => $data->_id]));
            },
        ],
        'Tel',
        'Email',
        [
            'attribute' => 'PremiumType',
            'value' => function (AppUser $data) {
                return $data->getPremiumType();
            },
            'filter' => Html::activeDropDownList(
                $searchModel,
                'PremiumType',
                [-1 => 'all'] + PremiumTypeEnum::getValues(),
                ['class' => 'form-control']
            ),
        ],
        [
            'attribute' => 'Language',
            'value' => function (AppUser $data) {
                return empty($data->Language) ? Yii::t('dict', 'Not set') : Yii::t('dict', Language::getValue($data->Language));
            },
        ],
        [
            'attribute' => 'CreationDate',
            'value' => function (AppUser $data) {
                $d = $data->CreationDate ?: new MongoDate(0);
                return $d->toDateTime()->format("Y-m-d");
            },
        ],
    ],
]);
Pjax::end();
