<?php

use app\components\MyHtml as Html;
use yii\widgets\DetailView;
use yii\bootstrap\Tabs;
use app\models\Language;
use app\models\User;

/**
 * @var $this yii\web\View
 * @var $model app\models\Journal
 */
?>
<?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        '_id',
        [
            'attribute' => "UserId",
            'value' => User::getNameById($model->UserId),
        ],
        "UserIp",
        "Operation",
        "ObjectId",
        "ObjectName",
        "AddedDate.sec:datetime",
        [
            'attribute' => 'Details',
            'value' => '<pre>' . print_r($model->Details, true) . '</pre>',
            'format' => 'raw'
        ]
    ],
])
?>
