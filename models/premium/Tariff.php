<?php

namespace app\models\premium;

use app\components\MongoActiveRecord;
use app\models\Language;
use Yii;

/**
 * This is the model class for table "Tariff".
 *
 * @property int $_id
 * @property int $Time
 * @property double $AproxPrice
 * @property string $DisplayPrice
 * @property bool $Enabled
 * @property \MongoDate $CreationDate
 * @property string[] $TitleArray
 * @property array $Title
 * @property string PayUrl
 */
class Tariff extends MongoActiveRecord
{
    public static function getDb()
    {
        return \Yii::$app->get('mongodb2');
    }

    /**
     * @return string the name of the index associated with this ActiveRecord class.
     */
    public static function collectionName()
    {
        return 'Tariff';
    }

    public function attributes()
    {
        return [
            "_id",
            "Time",
            "DisplayPrice",
            "AproxPrice",
            "Currency",
            "Enabled",
            "CreationDate",
            "Title",
            "PayUrl",
        ];
    }

    public function attributeHints()
    {
        return [
            'PayUrl' => 'Можно использовать макросы {TOKEN}, {USER_ID}, {TARIFF_ID}',
        ];
    }

    public function init()
    {
        $this->_id = 0;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function getCreationDateFormat()
    {
        return $this->CreationDate->sec;
    }

    public function getTimeFormat()
    {
        $month = floor($this->Time / 720);
        $days = floor(($this->Time % 720) / 24);
        $hours = floor(($this->Time % 720) % 24);

        $returnString = "";
        if (!empty($month)) {
            $returnString .= "$month месяцев ";
        }
        if (!empty($days)) {
            $returnString .= "$days дней ";
        }
        if (!empty($hours)) {
            $returnString .= "$hours часов";
        }

        return $returnString;
    }

    public function getFormTitle($lang = 'ru')
    {
        foreach ($this->Title as $quote) {
            if ($quote['Language'] = $lang) {
                return $quote['Quote'];
            }
        }
        return $this->Title[0]['Quote'];
    }

    public function getTitleArray()
    {
        $returnArray = [];
        if (empty($this->Title)) {
            $returnArray['ru'] = Language::getValue('ru');
        } else {
            foreach ($this->Title as $Quote) {
                $returnArray[$Quote['Language']] = $Quote['Quote'];
            }
        }
        return $returnArray;
    }

    public function setTitleArray($input)
    {
        $input = array_filter($input);
        $clean_array = [];
        foreach ($input as $lang_key => $lang_value) {
            $clean_array[] = [
                'Language' => $lang_key,
                'Quote' => $lang_value
            ];
        }
        $this->Title = $clean_array;
    }

    public function getEmptyTitleArray()
    {
        $notEmptyLangs = $this->TitleArray;
        $returnArray = [];
        foreach (Language::getList() as $lang_key => $lang_value) {
            if (!isset($notEmptyLangs[$lang_key])) {
                $returnArray[$lang_key] = $lang_value;
            }
        }
        return $returnArray;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [["DisplayPrice", 'Time'], 'required'],
            [['Enabled'], 'boolean'],
            [["AproxPrice"], 'string'],
            [["Time"], 'integer'],
            [["PayUrl"], 'url'],
            [["Currency", 'Enabled', 'Title', 'TitleArray'], 'safe']
        ];
    }

    public function beforeSave($insert)
    {
        if (empty($this->AproxPrice)) {
            $this->AproxPrice = floatval($this->DisplayPrice);
        }
        $this->Time = intval($this->Time);
        $this->DisplayPrice = strval($this->DisplayPrice);
        $this->AproxPrice = floatval($this->AproxPrice);
        $this->Enabled = boolval($this->Enabled);
        if ($insert) {
            $this->CreationDate = new \MongoDate();
        }
        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('dict', 'Id'),
            'Title' => Yii::t('dict', 'Tariff Title'),
            'Time' => Yii::t('dict', 'Tariff Time'),
            'DisplayPrice' => Yii::t('dict', 'Display price'),
            'AproxPrice' => Yii::t('dict', 'Aprox price'),
            'Currency' => Yii::t('dict', 'Currency'),
            'Enabled' => Yii::t('dict', 'Tariff Enabled'),
            'CreationDate' => Yii::t('dict', 'Creation Date'),
            'PayUrl' => Yii::t('dict', 'Url for payment'),
        ];
    }
}