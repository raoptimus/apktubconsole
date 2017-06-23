<?php
namespace app\models;

use Yii;
use yii\base\Model;

/**
 * Class Text
 * @package app\models
 */
class Text extends Model
{
    public $Quote;
    public $Language;

    public function rules()
    {
        return [
            [
                'Quote',
                'required',
            ],
            [
                'Language',
                'in',
                'range' => Language::getKeys(),
                'message' => Yii::t('app', 'Field is invalid.'),
            ],
        ];
    }

    public function attributes()
    {
        return [
            "Quote",
            "Language",
        ];
    }

    public static function getTranslatedQuote($listAttr, $lang = "ru")
    {
        if (empty($listAttr)) {
            return "";
        }

        foreach ($listAttr as $t) {
            if ($t['Language'] == $lang) {
                return $t['Quote'];
            }
        }

        return "";
    }
}