<?php
/**
 * Лог событий устройств
 *
 * @author ra ra@jabberz.net
 * @copyright 2016
 */

namespace app\models\users;

use app\components\MongoActiveRecord;
use MongoDate;
use MongoId;
use yii\data\ActiveDataProvider;
use Yii;
use yii\mongodb\Query;

/**
 * @property MongoId _id
 * @property MongoDate AddedDate
 * @property DeviceEventActionEnum Action
 * @property string DeviceId
 * @property string Details
 * @property string Ip
 * @property string Ver
 */
class DeviceEvent extends MongoActiveRecord
{
    public static function getDb()
    {
        return \Yii::$app->get('mongodb2');
    }

    public static function collectionName()
    {
        return 'DeviceEvent';
    }

    /**
     * @param string|string[] $deviceId
     * @param null|DeviceEventActionEnum::FLaunch $action
     * @return Query
     */
    public static function getQuery($deviceId, $action = null)
    {
        $q = [];

        if (!is_null($action) && in_array($action, DeviceEventActionEnum::getKeys())) {
            $q['Action'] = $action;
        }

        if (is_array($deviceId)) {
            $q['DeviceId'] = ['$in' => $deviceId];
        } else {
            $q['DeviceId'] = $deviceId;
        }

        return DeviceEvent::find()->where($q);
    }

    /**
     * @param Query $q
     * @return ActiveDataProvider
     */
    public static function getProvider(Query $q)
    {
        return new ActiveDataProvider([
            'query' => $q,
            'sort' => [
//                'attributes' => (new DeviceEvent)->attributes(),
                'defaultOrder' => [
                    'AddedDate' => SORT_DESC
                ]
            ],
            'pagination' => [
                'pageSize' => Yii::$app->params['deviceEventPageSize'],
            ],
        ]);
    }

    public function attributes()
    {
        return [
            "_id",
            "AddedDate",
            "Action",
            "DeviceId",
            "Details",
            "Ip",
            "Ver",
        ];
    }

    public function getActionTitle()
    {
        return DeviceEventActionEnum::getValueTranslated($this->Action);
    }
}