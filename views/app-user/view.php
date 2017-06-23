<?php

use app\models\users\Device;
use yii\widgets\DetailView;
use yii\bootstrap\Tabs;

/**
 * @var $this yii\web\View
 * @var $model app\models\users\AppUser
 */

$now = new \DateTime();

?>
<div class="admin-user-roles-view userView">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            '_id',
            'UserName',
            'Tel',
            'Email',
            'Language',
            [
                'attribute' => 'Premium.Expires',
                'value' => $model->isPremiumExpires()
                    ? 'Premium expired'
                    : $model->getPremiumExpires()->toDateTime()->format('Y-m-d H:i:s O')
            ],
            [
                'attribute' => 'Premium.Type',
                'value' => $model->getPremiumType()
            ],
            [
                'attribute' => 'CreationDate',
                'value' => date('Y-m-d H:i:s O', $model->CreationDate->sec)
            ]
        ],
    ]) ?>

    <h2><?= Yii::t('dict', 'Device list') ?></h2>
    <?php
    $t = $this;
    echo Tabs::widget([
        'items' => array_map(function (Device $device) use ($t) {
            return [
                'label' => $device->Manufacture . ' ' . $device->Model . ' ' . $device->Os . ' ' . $device->VerOs,
                'content' => $t->render('../device/_sharedView', [
                    'model' => $device,
                ]),
            ];
        }, $model->Devices),
    ]);
    ?>
</div>
