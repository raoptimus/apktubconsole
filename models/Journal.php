<?php

namespace app\models;


use app\components\MongoActiveRecord;
use Yii;

/**
 * This is the model class for table "Journal".
 *
 * @property \MongoId $_id
 * @property string $UserId
 * @property string $UserIp
 * @property string $Operation
 * @property integer $ObjectId
 * @property integer $ObjectName
 * @property string $Details
 * @property \MongoDate $AddedDate
 */
class Journal extends MongoActiveRecord
{
    public function attributes()
    {
        return [
            "_id",
            "UserId",
            "UserIp",
            "Operation",
            "ObjectId",
            "ObjectName",
            "Details",
            "AddedDate"
        ];
    }
    public static function getDb() {
        return \Yii::$app->get('mongodb2');
    }

    public function getId() {
        return (string) $this->_id;
    }

    /**
     * @return string the name of the index associated with this ActiveRecord class.
     */
    public static function collectionName()
    {
        return 'Journal';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [["UserId", "UserIp", "Operation", "ObjectId", "ObjectName", "AddedDate"], 'required'],
            [["_id", "UserId", "UserIp", "Operation", "ObjectId", "ObjectName"], 'string'],
            ['AddedDate', 'yii\mongodb\validators\MongoDateValidator', 'format' => 'MM/dd/yyyy'],
            [["Details"],'safe']
        ];
    }

    /**
     * @param string $objectName
     * @param string $operation
     * @param string $objectId
     * @param string $details
     * @return bool
     */
    public static function newEvent($objectName = '', $operation = '', $objectId = '', $details = '') {
        $event = new Journal();
        $event->UserId = self::getCurrentUserId();
        $event->UserIp = self::getCurrentUserIp();
        $event->AddedDate = new \MongoDate();
        $event->Operation = $operation;
        $event->ObjectName = $objectName;
        $event->ObjectId = $objectId;
        $event->Details = $details;

        $save = $event->save();

        if (!$save) {
            Yii::trace($event->errors);
        }

        return $save;
    }

    public static function getCurrentUserId()
    {
        return empty(Yii::$app->getUser()->getId()) ? 0 : Yii::$app->getUser()->getId();
    }
    public static function getCurrentUserIp()
    {
        return empty(Yii::$app->request->getUserIP()) ? '' : Yii::$app->request->getUserIP();
    }
}
