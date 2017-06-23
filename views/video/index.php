<?php
/**
 * @var $this \yii\web\View
 * @var $model \app\models\video\VideoFilter
 * @var $filterCats array
 */
use yii\bootstrap\Button;
use yii\web\View;
use yii\widgets\ListView;
use yii\widgets\Pjax;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use app\models\Language;
?>
<div>
    <?php
    Pjax::begin(['options' => ['class' => 'pjax']]);

        $form = ActiveForm::begin([
            'layout' => 'inline',
            'options' => ['data-pjax' => true, 'class' => 'well well-sm'],
            'action' => Url::toRoute("video/index"),
            'method' => 'get',
            'fieldConfig' => [
                'template' => "\n{input}&nbsp\n",
            ],
        ]);
        echo $form->field($model, "Search")->textInput(['placeholder' => $model->getAttributeLabel('Search')]);
        echo $form->field($model, "Language")->dropDownList(array_map(function($el) {return Yii::t('dict',$el);},Language::getList()));
        echo $form->field($model, "SortBy")->dropDownList([
            "PublishedDate" => Yii::t('dict',"By published date"),
            "UpdateDate" => Yii::t('dict',"By update date"),
            "Rank" => Yii::t('dict',"By rank (TOP)"),
            "_id" => Yii::t('dict',"by Id"),
            "Source.SourceId" => Yii::t('dict',"by SourceId"),
        ]);
        echo $form->field($model, "SortDirect")->dropDownList([SORT_ASC => Yii::t('dict','ASC'), SORT_DESC => Yii::t('dict','DESC')]);

        echo $form->field($model, "Status")->dropDownList([
            "*" => Yii::t('dict',"Active videos"),
            "approved" => Yii::t('dict',"Approved videos"),
            "!approved" => Yii::t('dict',"Not approved"),
            "published" => Yii::t('dict',"Published videos"),
            "deleted" => Yii::t('dict',"Deleted videos"),
        ]);

        echo $form->field($model, "Category")->dropDownList($filterCats);
        echo $form->field($model, "isPremium")->dropDownList(['*' => 'All', 'premium' => 'Premium', '!premium' => 'Not premium']);

        echo Button::widget([
            'label' => Yii::t('dict','Apply'),
            'options' => ['type' => 'submit', 'class' => 'btn-primary'],
        ]);
        ActiveForm::end();?>

        <?php echo ListView::widget([
            'layout' => '{items}<div class="clearfix"></div>{pager}',
            'dataProvider' => $model->getDataProvider(true),
            'itemView' => '_videoItem',
            'viewParams' => ['searchModel' => $model],
        ]);
    Pjax::end();

    $this->registerJsFile("http://cdn.12player..../last/tc-player.min.js", ['depends' => 'yii\web\JqueryAsset']);
    $this->registerJsFile("/js/image-picker.min.js", ['depends' => 'yii\web\JqueryAsset']);
    $this->registerJsFile("/js/acc.js", ['depends' => 'yii\web\JqueryAsset']);
    $this->registerCssFile("/css/image-picker.css");

    $script = "window.playerOptions = {}";
    $this->registerJs($script, View::POS_HEAD);


    $script = '$(".btn-tooltip").tooltip();
        $(".grid-search-trigger").change(function(){filterGrid(this)});
        ';
    $this->registerJs($script, View::POS_READY);
    ?>
</div>