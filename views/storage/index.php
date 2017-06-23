<?php

use app\models\storage\Storage;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use app\components\MyHtml as Html;
use kartik\daterange\DateRangePicker;

/**
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $searchModel app\models\JournalSearch
 * @var $counts array
 * @var $weight integer
 * */
?>
    <p class="pull-left">
        <?= Html::a(Yii::t('dict', Yii::t('dict', 'Create new storage')),
            ['storage/create'], ['class' => 'btn btn-success']) ?>
    </p>
    <div class="clearfix"></div>
<?php
$datePickerOptions = [
    'name' => 'date_range',
    'presetDropdown' => true,
    'pluginOptions' => [
        'locale' => ['format' => 'YYYY-MM-DD'],
        'opens' => 'left'
    ]
];

if (!empty($searchModel->CreationDateFrom) && !empty($searchModel->CreationDateTo)) {
    $datePickerOptions['value'] = date('Y-m-d', $searchModel->CreationDateFrom) . ' - ' .
        date('Y-m-d', $searchModel->CreationDateTo);
}

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
            'value' => function ($data) {
                return Html::a($data->id, Url::toRoute(['update', 'id' => $data->id]));
            },
        ],
        "Title",
        [
            'attribute' => 'StorageType',
            'format' => 'raw',
            'filter' => Html::activeDropDownList($searchModel, 'StorageType',
                ['all' => 'Все', 'swift' => 'swift', 'ftp' => 'ftp', 'rsync' => 'rsync'],
                ['class' => 'form-control']),
        ],
        "UsedSpace",
        "TotalFiles",
        [
            'attribute' => 'CreationDate',
            'value' => function (Storage $data) {
                return date('Y-m-d H:i:s', $data->getCreationDate());
            },
            'filter' => DateRangePicker::widget($datePickerOptions),
            'label' => 'Дата создания'
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{update}&nbsp;{delete}',
            'buttons' => [
                'delete' => function ($url, Storage $model) {
                    if ($model->UsedSpace > 0 || $model->TotalFiles > 0) {
                        return '';
                    }

                    return Html::a(
                        Html::glyphicon('trash'),
                        Url::toRoute([
                            'remove',
                            'id' => $model->_id,
                        ]),
                        [
                            'class' => 'btn btn-tooltip',
                            'title' => Yii::t("dict", 'Delete'),
                            'data-placement' => 'left',
                            'data-pjax' => '0',
                        ]
                    );

                },
            ],
        ],
    ],
]);
Pjax::end();