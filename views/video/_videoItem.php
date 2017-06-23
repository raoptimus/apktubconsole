<?php
/**
 * @var $searchModel \app\models\video\VideoFilter
 * @var $model \app\models\video\Video
 */
use app\components\MyHtml as Html;
use yii\helpers\Url;

$approved =  in_array("approved", $model->Filters);
$published = in_array("published", $model->Filters);

$statusColor = "#fcf8e3";
if ($published) {
    $statusColor = "#ffffff";
} else if ($approved) {
    $statusColor = "#d9edf7";
}

?>
<div class="col-xs-5 col-sm-4 col-md-3 col-lg-2 nopad">
    <div class="thumbnail text-center" style="background-color: <?= $statusColor ?>;">
        <a href="<?= Url::to(['video/edit', 'id' => $model->_id]) ?>">
                <?= Html::img("/img/loading.png",
                        [
                        'id' => "video_thumb_{$model->_id}",
                        "class" => "lazy",
                        "data-original" => '/'.Yii::$app->params['project']."/video/get-thumb?id={$model->_id}&size=300x180"
                    ]
                ) ?>
            <h5 class="video_block_title">
                <?= $model->getLangTitle($searchModel->Language) ?>
            </h5>
        </a>
        <div class="top_video_block_info" >
            <?= Html::glyphicon('hourglass') . ' ' . date('Y-m-d',$model->PublishedDate->sec)?>
        </div>
        <div class="video_block_info" >
            <?= $model->LikeCount > 0 ? '&nbsp;&nbsp;' . Html::glyphicon('thumbs-up') . ' ' . $model->LikeCount : '' ?>
            <?= $model->DownloadCount > 0 ? '&nbsp;&nbsp;' . Html::glyphicon('floppy-save') . ' ' . $model->DownloadCount : '' ?>
            <?= $model->CommentCount > 0 ? '&nbsp;&nbsp;' . Html::glyphicon('pencil') . ' ' . $model->CommentCount : '' ?>
            <?= $model->ViewCount > 0 ? '&nbsp;&nbsp;' . Html::glyphicon('eye-open') . ' ' . $model->ViewCount : '' ?>
        </div>

        <div class="btn-group btn-group-sm" role="group">
            <?= $this->render('buttons/_picture', [
                'model' => $model
            ]); ?>

            <button type="button" class="btn btn-default btn-tooltip" data-toggle="modal"
                    data-target="#show_video_<?= $model->_id ?>" data-placement="bottom" aria-label="Посмотреть видео"
                    data-original-title="Посмотреть видео">
                <span class="glyphicon glyphicon-play" aria-hidden="true"></span>
            </button>

            <?php
                echo $this->render('buttons/_up', [
                    'model' => $model
                ]);
                echo $this->render('buttons/_pushpin', [
                    'model' => $model
                ]);
                echo $this->render('buttons/_approve', [
                    'model' => $model
                ]);
                echo $this->render('buttons/_delete', [
                    'model' => $model
                ]);
            ?>
        </div>
    </div>
</div>
<div class="modal fade" id="show_video_<?= $model->_id ?>" data-video_id="<?= $model->_id ?>" tabindex="-1"
     role="dialog" aria-labelledby="show_video_<?= $model->_id ?>" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"
                    id="myModalLabel"><?= $model->getLangTitle($searchModel->Language) ?>
                    - Просмотр видео</h4>
            </div>
            <div class="modal-body video-list-modal-body" id="modal_body_video_<?= $model->_id ?>">
                <div class="videoList-videoContainer" id="video_container_<?= $model->_id ?>"></div>
                <div>
                    <p class="modal-paragraph">
                        <?= $model->getLangDesc($searchModel->Language) ?>
                    </p>
                </div>
                <?php
                $options = $model->getPlayerOptions();
                $script = "window.playerOptions[$model->_id] = " . json_encode($options) . ';
                $("#show_video_' . $model->_id . '").on("shown.bs.modal", function (e) {
                    $_tc.player(window.playerOptions[$(this).attr("data-video_id")]);
                });
                $("#show_video_' . $model->_id . '").on("hidden.bs.modal", function (e) {
                    $("#video_container_' . $model->_id . '").remove();
                    $("#modal_body_video_' . $model->_id . '").prepend("<div class=\'videoList-videoContainer\' id=\'video_container_' . $model->_id . '\'></div>");
                });';
                $this->registerJs($script, yii\web\View::POS_READY);
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary pull-left">Редактировать</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>






