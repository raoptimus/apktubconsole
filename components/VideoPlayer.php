<?php
namespace app\components;

use yii\base\Widget;
use yii\helpers\Html;


/**
 * @property array options
 * @property array clientEvents
 */
class VideoPlayer extends Widget
{
    const TYPE_AUTO = "auto";
    const TYPE_HTML5 = "html5";
    const TYPE_FLASH = "flash";

    public $options = [];
    public $tagName = "div";
    public $scriptPath = "http://cdn.12player..../last";
    public $hideControls = false;
    public $autoplay = false;
    public $type = "auto";
    public $preload = false;
    public $streamingParam = "start";
    public $playlist = [];
    public $defaultVideoPlay = -1;
    public $autoShow = true;

    public function addTrackToPlaylist($posterUrl, $videos = []) {
        foreach ($videos as $video) {
            if (!is_array($video) || !array_key_exists("url", $video)) {
                throw new \InvalidArgumentException("Argument videos is not valid");
            }
        }
        $this->playlist[] = [
            'poster' => $posterUrl,
            'videos' => $videos,
        ];
    }
    public function addVideoToTrack($index = 0, $height, $url) {
        if (!array_key_exists($index, $this->playlist)) {
            throw new \InvalidArgumentException("Track number of {$index} not exists in playlist");
        }
        $this->playlist[$index]['videos'][$height] = [
            'url' => $url,
        ];
    }

    /**
     * Initializes the widget.
     * If you override this method, make sure you call the parent implementation first.
     */
    public function init()
    {
        parent::init();
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
    }

    /**
     * Renders the widget.
     */
    public function run()
    {
        $view = $this->getView();
        $view->registerJsFile($this->scriptPath . "/tc-player.min.js", ['depends'=>'yii\web\JqueryAsset']);

        if ($this->autoShow) {
            $view->registerJs('$_tc.player('.$this->getJsonPlayerOptions().')');
        }

        return Html::tag($this->tagName, "", $this->options);
    }

    protected function getPlayerOptions() {
        $def = $this->defaultVideoPlay;
        return [
            'path' => $this->scriptPath,
            'containerId' => $this->options['id'],
            'hideControls' => $this->hideControls,
            'autoStart' => $this->autoplay,
            'defaultPlayer' => $this->type,
            'preload' => $this->preload,
            'streamingParam' => $this->streamingParam,
            'playlist' => array_map(function($track) use($def) {
                $videos = [];
                foreach ($track['videos'] as $k => $v) {
                    $videos[$k] = [
                        'fileUrl' => $v['url'],
                        'isDefault' => $k == $def,
                    ];
                }
                return [
                    'introImage' => $track['poster'],
                    'videos' => $videos,
                ];

            }, $this->playlist),
        ];
    }

    protected function getJsonPlayerOptions(){
        return json_encode($this->getPlayerOptions(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}