<?php
use app\components\MyHtml as Html;
use yii\widgets\DetailView;
use yii\bootstrap\Tabs;
use app\models\Language;
/**
 * @var $this yii\web\View
 * @var $model app\models\video\Category
 */

$items = ['_id', 'VideoCount'];
foreach ($model->titleArray as $lang_key => $title) {
    $items[] = [
        'label' => Language::getValue($lang_key),
        'value' => $title
    ];
}


echo DetailView::widget([
    'model' => $model,
    'attributes' => $items
])
?>
<div class="well well-sm clearfix">
    <?= Html::a(
        Html::glyphicon("pencil") . " " . Yii::t('dict', 'Edit'),
        ['update', 'id' => $model->_id],
        ['class' => 'btn btn-primary pull-left']
    ) ?>
</div>