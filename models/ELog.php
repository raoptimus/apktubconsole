<?php

namespace app\models;


use app\components\MongoActiveRecord as ActiveRecord;
use Yii;

class ELog extends ActiveRecord
{
    public static function getDb()
    {
        return \Yii::$app->get('mongodb2');
    }

    public function attributes()
    {
        return [
            "_id",
            "Priority",
            "Time",
            "Hostname",
            "Tag",
            "Msg",
            "Pid"
        ];
    }

    public function getId() {
        return (string) $this->_id;
    }

    public static function getPriority() {

    }

    /**
     * @return string the name of the index associated with this ActiveRecord class.
     */
    public static function collectionName()
    {
        return 'Log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [["_id", "Priority", "Hostname", "Tag", "Msg", "ObjectName"], 'string'],
            [["Pid"], 'integer'],
            [["Time"],'safe']
        ];
    }

    public function getReadableTime() {
        return $this->Time->sec;
    }

}