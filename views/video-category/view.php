<?php

use app\components\MyHtml as Html;
use yii\widgets\DetailView;
use yii\bootstrap\Tabs;
use app\models\Language;

/**
 * @var $this yii\web\View
 * @var $model app\models\video\Category
 */


echo Tabs::widget([
    'items' => array_map(function ($lang) use ($model) {
        return [
            'label' => Language::getValue($lang),
            'content' => $this->render('_langView',[
                'lang' => $lang,
                'model' => $model
            ])
        ];
    }, $model->getActiveLangs()),
])






?>

<?php
/* DetailView::widget([
    'model' => $model,
    'attributes' => [
        '_id',
        'SourceIdForm',
        'Info' => [
            'label' => $model->getAttributeLabel("Title"),
            'value' => Tabs::widget([
                'items' => array_map(function ($title) {
                    return [
                        'label' => Language::getValue($title['Language']),
                        'content' => '<div class="well">
                            <ul>
                                <li> Название - '. $title['Quote'] .'</li>
                                <li> Slug - '. $title['Quote'] .'</li>
                            </ul>


' . $title['Quote'] . '</div>',
                        'encode' => false,
                    ];
                }, $model->Title),
            ]),
            'format' => 'raw',
        ],
        'Slug' => [
            'label' => $model->getAttributeLabel("Slug"),
            'value' => Tabs::widget([
                'items' => array_map(function ($slug) {
                    return [
                        'label' => Language::getValue($slug['Language']),
                        'content' => '<div class="well">' . $slug['Quote'] . '</div>',
                        'encode' => false,
                    ];
                }, $model->SlugView),
            ]),
            'format' => 'raw',
        ],
        'LongDesc' => [
            'label' => $model->getAttributeLabel("LongDesc"),
            'value' => Tabs::widget([
                'items' => array_map(function ($lDesc) {
                    return [
                        'label' => Language::getValue($lDesc['Language']),
                        'content' => '<div class="well">' . $lDesc['Quote'] . '</div>',
                        'encode' => false,
                    ];
                }, $model->LongDescView),
            ]),
            'format' => 'raw',
        ],
        'ShortDesc' => [
            'label' => $model->getAttributeLabel("ShortDesc"),
            'value' => Tabs::widget([
                'items' => array_map(function ($sDesc) {
                    return [
                        'label' => Language::getValue($sDesc['Language']),
                        'content' => '<div class="well">' . $sDesc['Quote'] . '</div>',
                        'encode' => false,
                    ];
                }, $model->ShortDescView),
            ]),
            'format' => 'raw',
        ]
    ],
])*/
?>
<div class="well well-sm clearfix">
    <?= Html::a(
        Html::glyphicon("pencil") . " " . Yii::t('dict', 'Edit'),
        ['update', 'id' => $model->_id],
        ['class' => 'btn btn-primary pull-left']
    ) ?>
</div>