<?php

use app\models\storage\Files;
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
    <div class="well">
        Итого Видео: <strong><?= $counts['videos'] ?></strong><br>
        Итого Файлов: <strong><?= $counts['files'] ?></strong><br>
        Общий вес: <strong><?= Files::humanFilesize($weight) ?></strong>
    </div>
<?php

$createDateOptions = [
    'name' => 'date_range',
    'presetDropdown' => true,
    'pluginOptions' => [
        'locale' => ['format' => 'YYYY-MM-DD'],
        'opens' => 'left'
    ],
];

if (!empty($searchModel->CreationDateFrom) && !empty($searchModel->CreationDateTo)) {
    $createDateOptions['value'] = date('Y-m-d', $searchModel->CreationDateFrom) . ' - ' .
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
                return Html::a(substr($data->id, 0, 10) . '...', Url::toRoute(['view', 'id' => $data->id]));
//                return $data->id;
            },
        ],
        [
            'attribute' => 'List.Ext',
            'format' => 'raw',
            'value' => function ($data) {
                return $data->getExts();
            },
            'filter' => Html::activeDropDownList($searchModel, 'List.Ext', ['all' => 'Все', '.mp4' => '.mp4', '.flv' => '.flv'], ['class' => 'form-control']),
            'label' => 'Расширение файла'
        ],
        [
            'attribute' => 'List.H',
            'format' => 'raw',
            'value' => function ($data) {
                return $data->getHs();
            },
            'filter' => Html::activeDropDownList($searchModel, 'List.H', ['all' => 'Все', 'small' => '≤ 320', 'medium' => '≤ 480', 'large' => '≤ 720', 'huge' => '>720'], ['class' => 'form-control']),
            'label' => 'Высота'
        ],
        [
            'attribute' => 'List.W',
            'format' => 'raw',
            'value' => function ($data) {
                return $data->getWs();
            },
            'label' => 'Ширина'
        ],
        [
            'attribute' => 'List.VideoBitrate',
            'format' => 'raw',
            'value' => function ($data) {
                return $data->getVideoBitrates();
            },
            'label' => 'Видео битрейт'
        ],
        [
            'attribute' => 'List.AudioBitrate',
            'format' => 'raw',
            'value' => function ($data) {
                return $data->getAudioBitrates();
            },
            'label' => 'Аудио битрейт'
        ],
        [
            'attribute' => 'List.Duration',
            'format' => 'raw',
            'value' => function ($data) {
                return $data->getDurations();
            },
            'label' => 'Длительность'
        ],
        [
            'attribute' => 'List.Size',
            'format' => 'raw',
            'value' => function ($data) {
                $sizes = $data->getSizes();

                if (count($sizes) == 1) {
                    return number_format($sizes[0] / 1048576, 2, '.', '') . "Мб";
                }

                $sizeArray = [];
                foreach ($sizes as $size) {
                    $sizeArray[] = number_format($size / 1048576, 2, '.', '') . ' Мб';
                }
                return implode(' | ', $sizeArray);
            },
            'label' => 'Размеры'
        ],
        [
            'format' => 'raw',
            'attribute' => 'Size',
            'value' => function ($data) {
                return $data->humanFilesize($data->Size);
            },
            'label' => 'Общий размер'
        ],
        [
            'attribute' => 'CreationDate',
            'value' => function (Files $data) {
                return date('Y-m-d H:i:s', $data->getCreationDate());
            },
            'filter' => DateRangePicker::widget($createDateOptions),
            'label' => 'Дата создания'
        ],
        [
            'attribute' => 'Projects',
            'value' => function ($data) {
                return $data->projectsString;
            },
            'filter' => Html::activeDropDownList($searchModel, 'Projects', ['all' => 'Все'] + $searchModel::getProjects(), ['class' => 'form-control']),
        ],
        [
            'attribute' => 'StorageId',
            'format' => 'raw',
            'value' => function ($data) {
                $s = $data->getStorage();
                return $s ? $s->Title : "";
            },
            'label' => 'Хранилище',
            'filter' => Html::activeDropDownList($searchModel, 'StorageId',
                ['all' => 'Все'] + Storage::getList(),
                ['class' => 'form-control']),
        ],
    ],
]);
Pjax::end();