<?php

use app\components\MyHtml as Html;
use yii\widgets\DetailView;
use yii\bootstrap\Tabs;
use app\models\Language;
use app\models\User;

/**
 * @var $this yii\web\View
 * @var $model app\models\ELog
 */
?>
<?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        '_id',
        "Priority",
        "Time:datetime",
        "Hostname",
        "Tag",
        "Pid",
        [
            'attribute' => 'Msg',
            'value' => '<pre>' . print_r($model->Msg, true) . '</pre>',
            'format' => 'raw'
        ]
    ],
])
?>
