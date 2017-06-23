<?php

use app\models\users\Device;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use app\components\MyHtml as Html;

/**
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $model app\models\users\DeviceSearch
 * */

Pjax::begin();

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $model,
    'options' => [
        'class' => 'device-list'
    ],
    'columns' => [
        [
            'attribute' => '_id',
            'format' => 'raw',
            'value' => function ($data) {
                return Html::a(substr($data->_id, 0, 10) . '...', Url::toRoute(['view', 'id' => $data->_id]));
            },
        ],

        'Manufacture',
        'Model',
        'Os',
        'VerOs',

        'Source.Site',
        'Source.Landing',
        'Source.Ad',
        'Source.Apk',
        'Source.Ver',
        'PushClickCount',
        'PushSendedCount',
        'Type',
        [
            'attribute' => 'HasGoogleId',
            'format' => 'raw',
            'value' => function (Device $data) {
                return $data->HasGoogleId
                    ? Html::glyphicon('ok', ['style' => 'color:green;'])
                    : Html::glyphicon('remove', ['style' => 'color:red;']);
            },
            'filter' => Html::activeDropDownList(
                $model,
                'HasGoogleId',
                ['all' => 'Безразлично', 'true' => 'Есть', 'false' => 'Нет', 'null' => 'Не известно'],
                ['class' => 'form-control']
            ),
            'options' => ['width' => '145px'],
        ],
        'LastGeo.countrycode',
        [
            'attribute' => 'LastActiveTime',
            'value' => function (Device $data) {
                $d = $data->LastActiveTime ?: new MongoDate();
                return $d->toDateTime()->format("Y-m-d");
            },
        ],
    ],
]);
Pjax::end();