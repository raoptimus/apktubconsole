<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\video\Category;
use yii\bootstrap\Button;
Use yii\web\View;
use app\models\video\Channel;
use kartik\datetime\DateTimePicker;

/**
 * @var $this yii\web\View
 * @var $model app\models\video\Video
 *
 * */

?>
    <div class="row edit-video-row">
        <div class="col-md-8">
            <?php
            $form = ActiveForm::begin([
                'layout' => 'horizontal',
                'fieldConfig' => [
                    'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
                    'horizontalCssClasses' => [
                        'label' => 'col-sm-2',
                        'offset' => 'col-sm-offset-2',
                        'wrapper' => 'col-sm-8',
                        'error' => '',
                        'hint' => '',
                    ],
                ],
            ]);

            echo $this->render('_editForm', [
                'model' => $model,
                'form' => $form
            ]);

            echo $form->field($model, "CategoryId")->dropDownList(
                ArrayHelper::map(Category::find()->all(), "_id", "langAttr")
            );
            echo $form->field($model, "ChannelId")->dropDownList(
                ArrayHelper::map(Channel::find()->all(), "_id", "Title")
            );

            echo $form->field($model, "ActorsForm")->textInput(["class" => "form-control ActorsInput", 'id' => 'ActorsInput']);

            if (in_array('!approved', $model->Filters)) {
                echo $form->field($model, "PublishedDateForm")->widget(DateTimePicker::classname(), [
                    'options' => ['placeholder' => 'Enter event time ...'],
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'dd-mm-yyyy hh:ii'
                    ]
                ]);
            }

            echo Button::widget([
                'label' => Yii::t('dict', $model->isNewRecord ? 'Add' : 'Save'),
                'options' => ['type' => 'submit', 'class' => 'btn-primary col-md-offset-2 col-sm-offset-2 '],
            ]);

            if (in_array('!approved', $model->Filters)) {
                echo Button::widget([
                    'label' => Yii::t('dict', $model->isNewRecord ? 'Add and publish' : 'Save and publish'),
                    'options' => ['type' => 'submit', 'class' => 'btn-primary col-md-offset-2 col-sm-offset-2', 'id' => 'save-and-publish', 'name' => 'publish', 'value' => 'publish'],
                ]);
            }

            ActiveForm::end();
            ?>
        </div>
        <div class="col-md-4">
            <div style="height:40px;">
                <div class="btn-group btn-group-sm" role="group">
                    <?php
                    echo $this->render('buttons/_picture', [
                        'model' => $model
                    ]);
                    echo $this->render('buttons/_up', [
                        'model' => $model
                    ]);
                    echo $this->render('buttons/_pushpin', [
                        'model' => $model
                    ]);
                    echo $this->render('buttons/_delete', [
                        'model' => $model
                    ]);
                    echo $this->render('buttons/_approve', [
                        'model' => $model
                    ]);
                    ?>
                </div>
            </div>

            <?php
            $sm = new \app\models\video\VideoFilter();
            $sm->Language = 'ru';
            echo $this->render('_play', [
                'model' => $model
            ]);
            ?>
        </div>
    </div>
<?php
$this->registerJsFile("/js/tagsinput/bootstrap-tagsinput.min.js", ['depends' => 'yii\web\JqueryAsset']);
$this->registerCssFile("/css/tagsinput/bootstrap-tagsinput.css");
$this->registerCssFile("/css/typeahead.min.css");

$this->registerJsFile("http://cdn.12player..../last/tc-player.min.js", ['depends' => 'yii\web\JqueryAsset']);
$this->registerJsFile("/js/typeahead.js", ['depends' => 'yii\web\JqueryAsset']);
$this->registerJsFile("/js/image-picker.min.js", ['depends' => 'yii\web\JqueryAsset']);
$this->registerJsFile("/js/acc.js", ['depends' => 'yii\web\JqueryAsset']);
$this->registerCssFile("/css/image-picker.css");

$script = "window.playerOptions = {}";
$this->registerJs($script, View::POS_HEAD);


$script = '$(".btn-tooltip").tooltip();
        $(".grid-search-trigger").change(function(){filterGrid(this)});
        ';
$this->registerJs($script, View::POS_READY);

$script = "
var tags = new Bloodhound({
  datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
  queryTokenizer: Bloodhound.tokenizers.whitespace,
  remote: {
    url: '/tag/hound?',
    replace: function(url, uriEncodedQuery) {
        var lang = $($('input').filter(':focus')[0]).parent().parent().parent().find('.myTagsInput').attr('data-lang')
        var res = (url + 'lang=' + lang + '&query=' + encodeURIComponent(uriEncodedQuery));
        return res
    }
  }
});
tags.initialize();

$('.myTagsInput').tagsinput({
  typeaheadjs: {
    name: 'tags',
    displayKey: 'name',
    valueKey: 'name',
    source: tags.ttAdapter()
  }
});";
$this->registerJs($script, View::POS_READY);

$script = "
var actors = new Bloodhound({
  datumTokenizer: Bloodhound.tokenizers.obj.whitespace('Name'),
  queryTokenizer: Bloodhound.tokenizers.whitespace,
  remote: {
    url: '/actor/hound?q=%QUERY',
    wildcard: '%QUERY'
  }
});
tags.initialize();

$('.ActorsInput').tagsinput({
  typeaheadjs: {
    name: 'actors',
    displayKey: 'Name',
    valueKey: 'Name',
    source: actors.ttAdapter()
  }
});
var actorInput = $('.ActorsInput').tagsinput('input');
actorInput.blur(function (e) {
    value = $(this).val();
    if (value == '') {
        //relax
    } else {
        $('.ActorsInput').tagsinput('add',value)
    }
    $(this).val('');
});


";
$this->registerJs($script, View::POS_READY);


?>