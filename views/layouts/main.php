<?php
use app\components\MyHtml as Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

/**
 * @var string $content
 * @var $this \yii\web\View
 */
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php
$this->beginBody();
echo Html::alert();
?>
<div class="wrap">
    <?= $this->render("_headerMenu") ?>
    <div class="container-fluid">
        <header class="well well-sm clearfix">
            <div class="pull-left">
                <a href="<?= Url::canonical() ?>">
                    <?= Html::glyphicon("refresh") ?>
                </a>

            </div>
            <div class="pull-left">
                <?= Breadcrumbs::widget([
                    'links' => Yii::$app->controller->breadcrumbs,
                    'homeLink' => false,
                ]) ?>
                <h1>
                    <a href="<?= Url::canonical() ?>"><?= Yii::t("dict", Yii::$app->controller->pageTitle); ?></a>
                </h1>
            </div>

        </header>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; ...Apk <?= date('Y') ?></p>
        <p class="pull-right">Server time: <?= date('Y-m-d H:i:s O') ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
