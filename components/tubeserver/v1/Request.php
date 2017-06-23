<?php

namespace app\components\tubeserver\v1;

use app\components\Enum;
use Yii;

class Request
{
    /**
     * @var string
     */
    public $Ip;
    /**
     * @var string
     */
    public $Token;
    /**
     * @var Language the language key
     */
    public $Lang;
    /**
     * @var array
     */
    public $Query;
    /**
     * @var \stdClass|array|null
     */
    public $Object;
    /**
     * @var SortInfo
     */
    public $Sort;
    /**
     * @var Page
     */
    public $Page;
    /**
     * @var string
     */
    public $Project;
    /**
     * @var string
     */
    public $Ver;

    function __construct()
    {
        $this->Ip = $_SERVER['REMOTE_ADDR'];
        $this->Project = Yii::$app->params['projectName'];
        //todo create the token for project
        $this->Token = "00001ac00512801af533a90eea5db269";// md5($this->Project);
        $this->Lang = Language::getKey(Language::getValue(Yii::$app->language));
    }
}

class SortInfo
{
    const DIRECT_DESC = -1;
    const DIRECT_ASC = 1;

    /**
     * @var string Name of the field for sorting
     */
    public $Field;
    /**
     * @var self::DIRECT_DESC|self::DIRECT_ASC
     */
    public $Direct;

    /**
     * @param string $field
     * @param int $direct SortInfo::DIRECT_DESC|SortInfo::DIRECT_ASC
     */
    function __construct($field, $direct = self::DIRECT_ASC)
    {
        $this->Field = $field;
        $this->Direct = (int)$direct;
    }
}

class Page
{
    public $Skip;
    public $Limit;

    function __construct($skip = 0, $limit = 100)
    {
        $this->Skip = (int)$skip;
        $this->Limit = (int)$limit;
    }
}

class Language extends Enum
{
    static $_values = [
        "ru" => "Russian",
        "en" => "English",
        "zh" => "Chinese",
        "es" => "Spanish",
        "ar" => "Arabic",
        "hi" => "Hindi",
        "bn" => "Bengali",
        "pt" => "Portuguese",
        "ja" => "Japanese",
        "de" => "German",
        "fr" => "French",
        "ko" => "Korean",
        "ta" => "Tamil",
        "it" => "Italian",
        "ur" => "Urdu",
        "tr" => "Turkish",
        "pl" => "Polish",
        "ms" => "Malay",
        "fa" => "Persian",
        "nl" => "Dutch",
    ];

    public static function getValue($key)
    {
        if (!isset(static::$_values[$key])) {
            return $key;
        }
        return static::$_values[$key];
    }
}