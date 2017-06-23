<?php
/**
 * @var $model \app\models\video\Category
 * @var $lang string
 */
use yii\widgets\DetailView;

echo DetailView::widget([
    'model' => $model,
    'attributes' => [
        '_id',
        'SourceIdForm',
        'Title' => [
            'label' => $model->getAttributeLabel("Title"),
            'value' => $model->getLangAttr('Title', $lang),
            'format' => 'raw',
        ],
        'Slug' => [
            'label' => $model->getAttributeLabel("Slug"),
            'value' => $model->getLangAttr('Slug', $lang),
            'format' => 'raw',
        ],
        'LongDesc' => [
            'label' => $model->getAttributeLabel("LongDesc"),
            'value' => $model->getLangAttr('LongDesc', $lang),
            'format' => 'raw',
        ],
        'ShortDesc' => [
            'label' => $model->getAttributeLabel("ShortDesc"),
            'value' => $model->getLangAttr('ShortDesc', $lang),
            'format' => 'raw',
        ]
    ]
]);
