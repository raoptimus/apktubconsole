<?php
/**
 * @var $this \yii\web\View
 * @var $model \app\models\video\CategorySearch
 */
use yii\bootstrap\Button;
use yii\widgets\Pjax;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use app\models\video\Category;
use app\components\MyHtml as Html;
use app\models\Language;
?>
<div>
    <div class="well well-sm clearfix">
        <?php
        echo Html::a(
            Html::glyphicon("plus") . " " . Yii::t("dict", "Create"),
            ['/video-category/create'],
            ['class' => 'btn btn-primary pull-right']
        );
        $form = ActiveForm::begin([
            'layout' => 'inline',
            'options' => ['data-pjax' => true, 'class' => 'pull-left'],
            'action' => Url::to(["video-category/index"]),
            'method' => 'get',
            'fieldConfig' => [
                'template' => "\n{input}&nbsp\n",
            ],
        ]);
        echo $form->field($model, "Search")->textInput(['placeholder' => $model->getAttributeLabel('Search')]);
        echo $form->field($model, "Language")->dropDownList(Language::getList());
        echo Button::widget([
            'label' => 'Apply',
            'options' => ['type' => 'submit', 'class' => 'btn-primary'],
        ]);

        ActiveForm::end();
        ?>
    </div>
    <?php
    Pjax::begin(['options' => ['class' => 'pjax']]);
    echo GridView::widget([
        'layout' => '{items}{pager}',
        'dataProvider' => $model->getDataProvider(),
        'columns' => [
            '_id',
            'Title' => [
                'label' => 'Title',
                'value' => function (Category $v) use ($model) {
                    return $v->getLangAttr('Title');
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} &nbsp; &nbsp; {update}',
            ],
        ]
    ]);
    Pjax::end();
    ?>
</div>