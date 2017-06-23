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
        "Title",
        "DisplayPrice",
        "AproxPrice",
        "Currency",
        "Enabled",
        "CreationDate",
    ],
])
?>
