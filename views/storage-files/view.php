<?php

use app\components\MyHtml as Html;
use app\models\storage\Storage;
use yii\widgets\DetailView;
use yii\bootstrap\Tabs;
use app\models\Language;
use app\models\User;

/**
 * @var $this yii\web\View
 * @var $model app\models\Storage\Files
 */
?>
<?php
echo DetailView::widget([
    'model' => $model,
    'attributes' => [
        '_id',
        [
            'attribute' => "CreationDate",
            'label' => 'Дата создания',
            'format' => 'raw',
            'value' => date('Y-m-d H:i:s', $model->getCreationDate())
        ],
        [
            'attribute' => "StorageId",
            'label' => 'Хранилище',
            'format' => 'raw',
            'value' => Storage::getValue($model->StorageId)
        ],
        [
            'label' => 'Длительность',
            'format' => 'raw',
            'value' => $model->getDurations()
        ],
    ],
]);
?>
<h2>Подробная информация о файлах:</h2>
<?php
foreach ($model->List as $file) {
    echo('<pre>');
    print_r($file);
    echo('</pre>');
}
?>