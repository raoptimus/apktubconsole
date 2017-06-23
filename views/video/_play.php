<?php
/* @var $model \app\models\video\Video */
use app\components\VideoPlayer;
?>
<div class="embed-responsive embed-responsive-16by9">
<?php
$player = VideoPlayer::begin();
$player->defaultVideoPlay = 480;
$player->addTrackToPlaylist($model->posterUrl());
$player->options['class'] = 'embed-responsive-item';
foreach ($model->Files as $f)
{
    $player->addVideoToTrack(0, $f['H'], $model->getFileUrl($f));
}
VideoPlayer::end();
?>
</div>
