<?php

namespace app\models\video;

use Yii;
use yii\base\Model;
use app\models\video\VideoTask;

class VideoTaskForm extends Model
{
    public $Url;
    public $File;
    public $Title;
    public $Desc;
    public $Tags;
    public $Models;
    public $Projects;
    public $Length;
    public $Offset;

    const ALLOWED_EXTENSIONS = "torrent, mp4, avi, flv, ogv";

    public function rules()
    {
        return [
            // UrlValidator cannot check file:// scheme easily so fuck it
            // ["Url", "url",],
            ["Url", "required", "on" => "default",],
            [
                "File", "file",
                "extensions" => self::ALLOWED_EXTENSIONS,
                "on" => "upload",
            ],
            [[
                "Title",
                "Desc",
                "TagsForm",
                "ModelsForm",
                "ProjectsForm",
                "Length",
                "Offset",
            ], "safe"]

        ];
    }

    public function attributeLabels()
    {
        return [
            "ProjectsForm" => Yii::t("dict", "Projects"),
            "ModelsForm" => Yii::t("dict", "Models"),
            "TagsForm" => Yii::t("dict", "Tags"),
        ];
    }


    public function getTagsForm()
    {
        return self::join($this->Tags);
    }

    public function setTagsForm($tags)
    {
        $this->Tags = self::split($tags);
    }

    public function getModelsForm()
    {
        return self::join($this->Models);
    }

    public function setModelsForm($models)
    {
        $this->Models = self::split($models);
    }

    public function getProjectsForm()
    {
        return self::join($this->Projects);
    }

    public function setProjectsForm($projects)
    {
        $this->Projects = self::split($projects);
    }

    public function upload()
    {
        if ($this->validate())
        {
            $dir = Yii::$app->params['uploadDir'];
            $filename = $this->File->baseName . '.' . $this->File->extension;
            $path = Yii::getAlias("@webroot") . $dir . $filename;

            $this->File->saveAs($path);

            if ($this->File->extension == "torrent")
                $this->Url = "http://" . Yii::$app->request->serverName . $dir . $filename;
            else
                $this->Url = "file://" . $path;

            return true;
        }

        return false;
    }

    // send makes an RPC call to the vc.manager
    public function send()
    {
        try
        {
            Yii::$app->videoManager->createTask($this);
        }
        catch(\Exception $e)
        {
            $this->addError("", $e->getMessage());
            return false;
        }
        return true;
    }

    private static function split($string)
    {
        $arr = array_map("trim", explode(",", $string));
        return array_unique(array_filter($arr));
    }

    private static function join($arr)
    {
        //$arr can be null
        return is_array($arr) ? join(", ", $arr) : "";
    }

}
