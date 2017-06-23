<?php
namespace app\components;
use \InvalidArgumentException;
/**
 * class CategoryEnum extends Enum {
 *     protected static $_values = [
 *         25003 => "ADULT",
 *         24000 => "NONE"
 *     ];
 *
 *     // optional for IDE autocomplete
 *     public static function NONE()
 *     {
 *         return self::get("NONE");
 *     }
 * }

 * $e = new CategoryEnum(CategoryEnum::NONE());
 * switch($e->key)
 * {
 *   case CategoryEnum::NONE():
 *       dump("1 ok");
 * }
 *
 * $e->value = "ADULT";
 * switch($e) {
 *   case "ADULT":
 *       dump("2 ok");
 * }
 *
 * $e->key = CategoryEnum::NONE();
 * print($e == "NONE", "$e", $e->key, $e->value);
 * print(array_keys(Category::getList()));
 *
 */

/**
 * Class Enum
 * @property string value
 * @property int|string key
 */
abstract class Enum
{
    /**
     *  @var [int => string]
     */
    protected static $_values = [];
    // used as third arg to dict
    protected static $_gender = 0; // 0=он, 1=она, 2=оно,3=они
    protected $_key;

    /**
     * @param int|string $keyOrValue Значение или ключ
     */
    public function __construct($keyOrValue = null)
    {
        if (is_null($keyOrValue))
        {
            $this->_key = null;
            return;
        }

        if (isset(static::$_values[$keyOrValue]))
        {
            $this->_key = $keyOrValue;
            return;
        }

        $this->_key = self::getKey($keyOrValue);
    }

    /**
     *
     * @param string $idAttr
     * @param string $valAttr
     * @return array [$idAttr => $key, $valAttr => $value]
     */
    public function getKeyValue($idAttr = "id", $valAttr = "title")
    {
        return [$idAttr => $this->key, $valAttr => $this->value];
    }

    /**
     *
     * @param string $idAttr
     * @param string $valAttr
     * @return array [[$idAttr => $key, $valAttr => $value], ...]
     */
    public static function getListKeyValue($idAttr = "id", $valAttr = "title")
    {
        $list = [];

        foreach (self::getList() as $k => $v)
        {
            $list[] = [$idAttr => $k, $valAttr => $v];
        }

        return $list;
    }

    public static function getListKeyValueTranslated($idAttr = "id", $valAttr = "title")
    {
        $list = [];

        foreach (self::getList() as $k => $v)
        {
            $list[] = [$idAttr => $k, $valAttr => static::translate($v)];
        }

        return $list;
    }

    public function __get($field)
    {
        switch ($field)
        {
          case "key":
              return $this->_key;

          case "value":
              return self::getValue($this->_key);
        }

        throw new \Exception("Access to undeclared field of enum");
    }

    public function __set($field, $arg)
    {
        switch ($field)
        {
          case "key":
              $this->_key = $arg;
                break;

          case "value":
              $this->set($arg);
                break;
        }

        throw new \Exception("Access to undeclared field of enum");
    }

    private function set($value)
    {
        $key = array_search($value, static::$_values);

        if (false === $key)
            throw new \Exception("Cannot find value {$value} in " . get_class() . " enum");

        $this->_key = $key;
    }

    /* * * * */

    protected static function get($value)
    {
        $key = array_search($value, static::$_values);

        if($value == 'Preroll'){
            var_dump(array_search($value, static::$_values));
            var_dump(static::$_values);
            var_dump($value);
            exit();
        }



        if ($key === false)
            throw new \Exception("Value {$value} not found in " . get_called_class() . " enum");

        return $key;

    }

    public static function __callStatic($name, $arguments)
    {
        return static::get($name);
    }

    public function __toString()
    {
        return static::$_values[$this->_key];
    }

    /**
       @param Enum|string $value
     */
    public static function getKey($value)
    {
        $k = array_search($value, static::$_values, true);

        if ($k === false)
            throw new InvalidArgumentException("Key not found for value '{$value}'");

        return $k;
    }

    public static function getKeys()
    {
        return array_keys(static::getList());
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public static function isContains($key)
    {
        return isset(static::$_values[$key]);
    }

    public static function getValue($key)
    {
        if (!isset(static::$_values[$key]))
            throw new InvalidArgumentException("Value not found by key '{$key}'");

        return static::$_values[$key];
//        return (isset(static::$_values[$key])) ? static::$_values[$key] : null;
    }

    public static function getValueTranslated($key)
    {
        return static::translate(self::getValue($key));
    }

    public static function getValues()
    {
        return array_values(static::getList());
    }

    public static function getList()
    {
        return static::$_values;
    }

    public static function getListTranslated()
    {
        return array_map(function($v) {
            return static::translate($v);

        }, static::$_values);
    }

    public static function getSortedList()
    {
        $list = static::$_values;
        ksort($list);

        return $list;
    }

    public static function moveOtherDown($list)
    {
        $needle = "Other";
        $key = array_search($needle, $list);
        unset($list[$key]);
        $list[$key] = $needle;

        return $list;
    }

    public static function translate($value)
    {
        return \Yii::t("dict", $value, static::$_gender);
    }
}