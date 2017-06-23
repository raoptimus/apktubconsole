<?php

use app\components\MyHtml as Html;
use app\models\Language;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Button;
use yii\bootstrap\Tabs;

/**
 * @var $this yii\web\View
 * @var $model app\models\video\Category
 */

$form = ActiveForm::begin([
    'layout' => 'default',
    'options' => ['class' => 'well'],
    'fieldConfig' => [
        'template' => "<br>{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
        'horizontalCssClasses' => [
            'label' => 'col-sm-2',
            'offset' => 'col-sm-offset-2',
            'wrapper' => 'col-sm-8',
            'error' => '',
            'hint' => '',
        ],
    ],
]);
echo $form->errorSummary($model);

echo Tabs::widget([
    'encodeLabels' => false,
    'items' => array_merge(
        array_map(function ($lng) use ($model, $form) {
            return [
                'label' => Language::getValue($lng),
                'content' => $this->render('_langEdit',[
                    'lng' => $lng,
                    'model' => $model,
                    'form' => $form,
                ]),
            ];

        }, array_keys($model->TitleForm)),
        [
            [
                'label' => Html::glyphicon("plus"),
                'items' => array_map(function ($lng) use ($model, $form) {
                    return [
                        'label' => Language::getValue($lng),
                        'options' => ['class' => 'nav-tab-lang'],
                        'content' => $this->render('_langEdit',[
                            'lng' => $lng,
                            'model' => $model,
                            'form' => $form,
                        ]),
                    ];
                }, array_diff(Language::getKeys(), array_keys($model->TitleForm))),
            ]
        ]

    ),
]);

echo $form->field($model, "SourceIdForm")->textInput();
?>
    <div class="form-group">
        <?php
        echo Button::widget([
            'label' => Yii::t('dict', $model->isNewRecord ? 'Create' : 'Save'),
            'options' => ['type' => 'submit', 'class' => 'btn-primary'],
        ]);
        ?>
    </div>
<?php
ActiveForm::end();
