<?php
namespace app\models\push;

use app\components\MongoActiveRecord;
use Yii;

/**
 * Class NotifyReturn
 * @property int ElapseDaysLastActiveFrom
 * @property int ElapseDaysLastActiveTo
 * @package app\models\push
 */
class NotifyReturn extends MongoActiveRecord
{
    public function rules()
    {
        return [
            [
                ['ElapseDaysLastActiveFrom','ElapseDaysLastActiveTo'],
                'filter',
                'filter' => 'intval',
            ],
            [
                ['ElapseDaysLastActiveFrom','ElapseDaysLastActiveTo'],
                'number',
                'min' => 0,
            ],
            [
                ['ElapseDaysLastActiveFrom','ElapseDaysLastActiveTo'], 'required'
            ],
        ];
    }

    public function attributes()
    {
        return [
            'ElapseDaysLastActiveFrom',
            'ElapseDaysLastActiveTo',
        ];
    }

    public function attributeLabels()
    {
        return [
            "ElapseDaysLastActiveFrom" => Yii::t("dict", "Elapse days last active FROM"),
            "ElapseDaysLastActiveTo" => Yii::t("dict", "Elapse days last active TO")
        ];
    }

    public function init()
    {
        $this->ElapseDaysLastActiveFrom = 3;
        $this->ElapseDaysLastActiveTo = 5;
    }
}