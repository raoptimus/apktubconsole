<?php

use yii\widgets\Pjax;
use app\models\Country;
use kartik\widgets\Select2;
use yii\web\View;
use app\components\MyHtml as Html;
use nterms\pagesize\PageSize;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel app\models\AdsSearch */

$this->title = Yii::t('dict', 'Ads list');
$this->params['breadcrumbs'][] = $this->title;

Pjax::begin();

?>
<p class="pull-left">
    <?= yii\helpers\Html::a(Yii::t('dict', 'Create new ads'), ['ads/create'], ['class' => 'btn btn-success']) ?>
</p>
<div class="col-md-1 pull-right">
    <?= PageSize::widget([
        'options' => [
            'class' => 'form-control'
        ],
        'label' => false
    ]);?>
</div>
<div class="clearfix"></div>
<?php

$options = [
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'filterSelector' => 'select[name="per-page"]',
    'columns' => [
        'Note',
        [
            'attribute' => 'Name',
            'value' => function ($model) {
                return $model->getLangAttribute('Name','ru');
            }
        ],
        [
            'attribute' => 'Title',
            'value' => function ($model) {
                return $model->getLangAttribute('Title','ru');
            }
        ],
        [
            'attribute' => 'Countries',
            'value' => function ($model) {
                $diff = array_diff(Country::getAllCountiesCodes(),$model->Countries);
                if (empty($diff)) {
                    return 'Все';
                }
                return implode(', ', $model->Countries);
            },
            'filter'=> Select2::widget([
                'model' => $searchModel,
                'attribute' => 'Countries',
                'data' => Country::getCodeTitleList(),
                'options' => ['placeholder' => 'Выберите страну'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]),
            'options' => [
                'width' => '400'
            ]
        ],
        'Age',
        'Rating',
        'Sort',
        [
            'attribute' => 'Status',
            'value' => function ($m) {
                return Html::glyphicon($m->Status == 'Running' ? "play" : "stop",
                    [
                        'style' => 'color: ' . ($m->Status == 'Running' ? "green" : "red"),
                    ]);
            },
            'filter'=> Html::activeDropDownList($searchModel, 'Status', ['All' => 'All', 'Running' => 'Running', 'Stopped' => 'Stopped'], ['class' => 'form-control']),
            'format' => 'html'

        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{update}&nbsp;&nbsp;{delete}',
        ],
    ],
    'layout' => "{items}\n{pager}",
    'options' => [
        'id' => 'sortableGrid'
    ]
];

//странно выглядит, но работает
$currentSort = Yii::$app->request->get('sort');

if (empty($currentSort) || $currentSort == 'Sort') {
    echo call_user_func('\himiklab\sortablegrid\SortableGridView::widget',$options);
} else {
    echo call_user_func('\yii\grid\GridView::widget',$options);
}

Pjax::end();