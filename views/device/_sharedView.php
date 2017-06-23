<?php
use app\models\users\Device;
use app\models\users\DeviceEvent;
use yii\data\ActiveDataProvider;
use yii\widgets\DetailView;
use app\components\MyHtml as Html;
use yii\grid\GridView;
use yii\web\View;

/**
 * @var $model Device
 */
?>

<div class="col-md-8">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            '_id',
            'Source.Site',
            'Source.Landing',
            'Source.Ad',
            'Source.Apk',
            'Source.Ver',
            'PushSendedCount',
            'PushClickCount',
            [
                'attribute' => 'HasGoogleId',
                'format' => 'raw',
                'value' => $model->HasGoogleId ? "yes" : "no",
            ],
            'GoogleId',
            [
                'attribute' => 'UpdateGoogleId',
                'format' => 'raw',
                'value' => empty($model->UpdateGoogleId) ?: $model->UpdateGoogleId->toDateTime()->format("Y-m-d H:i:s O"),
            ],
            'Type',
            'Serial',
            'SerialGsm',
            'LaunchCount',
            'DownloadCount',
            'ExitCount',
            'LastIp',
            'LastGeo.countryname',
            [
                'attribute' => 'LastActiveTime',
                'value' => empty($model->LastActiveTime) ?: $model->LastActiveTime->toDateTime()->format("Y-m-d H:i:s O"),
            ],
            [
                'attribute' => 'TimeLocation',
                'value' => $model->DateTimeLocation()->format("Y-m-d H:i:s O") . " +" . $model->Loc['Gmt'],
            ]
        ],
    ]) ?>
</div>
<div class="col-md-4">
    <?php $this->registerJsFile("http://api-maps.yandex.ru/2.1/?lang=ru_RU", ['depends' => 'yii\web\JqueryAsset']); ?>
    <?= Html::tag('div', '', ['style' => 'height: 400px;', 'id' => 'lastGeo_' . $model->_id]) ?>
    <?php
    $script = "ymaps.ready(initMap{$model->_id});
                var Map{$model->_id};
                function initMap{$model->_id}(){
                    Map = new ymaps.Map('lastGeo_{$model->_id}', {
                        center: [{$model->LastGeo['latitude']}, {$model->LastGeo['longitude']}],
                        zoom: 7
                    });
                    Placemark{$model->_id} = new ymaps.Placemark([{$model->LastGeo['latitude']}, {$model->LastGeo['longitude']}], { content: 'Last Geo', balloonContent: 'Last Geo' });
                    Map.geoObjects.add(Placemark{$model->_id});
                }";
    $this->registerJs($script, View::POS_READY);
    ?>
</div>
<div class="col-md-12">
    <h2><?= Yii::t('dict', 'History') ?></h2>
    <?= GridView::widget([
        'dataProvider' => new ActiveDataProvider([
            'query' => DeviceEvent::getQuery($model->_id),
            'sort' => [
                'defaultOrder' => [
                    'AddedDate' => SORT_DESC
                ]
            ],
            'pagination' => [
                'pageSize' => Yii::$app->params['deviceEventPageSize'],
            ],
        ]),
        'columns' => [
            [
                'attribute' => 'AddedDate',
                'label' => Yii::t('dict', 'Action Date'),
                'value' => function (DeviceEvent $item) {
                    return $item->AddedDate->toDateTime()->format('Y-m-d H:i:s O');
                },
            ],
            [
                'attribute' => 'Action',
                'label' => Yii::t('dict', 'Action'),
                'value' => function (DeviceEvent $item) {
                    return $item->getActionTitle();
                },
            ],
            [
                'attribute' => 'Details',
                'label' => Yii::t('dict', 'Details'),
                'value' => function (DeviceEvent $item) {
                    return $item->Details;
                },
            ]
        ],
    ]);
    ?>
</div>