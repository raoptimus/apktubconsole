<?php
/**
 * Created by PhpStorm.
 * User: sainomori
 */

namespace app\models\users;

use app\components\MongoActiveRecord;
use MongoDate;
use MongoId;
use Yii;

/**
 * @property MongoId _id
 * @property string Manufacture
 * @property string Model
 * @property string Os
 * @property string VerOs
 * @property bool HasGoogleId
 * @property string GoogleId
 * @property mixed LastGeo
 * @property int ExitCount
 * @property MongoDate UpdateGoogleId
 * @property MongoDate LastActiveTime
 * @property array Log
 */
class Device extends MongoActiveRecord
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
        return 'Device';
    }

    public static function getCarrierTypes()
    {
        return ['wifi' => 'Wi-Fi', 'mobile' => 'мобильный интернет'];
    }

    /**
     * @return \DateTime
     */
    public function DateTimeLocation()
    {
        if (empty($this->Loc["Loc"])) {
            return new \DateTime("1970-01-01");
        }
        return new \DateTime('now', new \DateTimeZone($this->Loc["Loc"]));
    }

    public function init()
    {
        $this->ExitCount = 0;
        $this->setAttribute('Source.Ver', '');
    }

    /**
     * @return array list of attribute names.
     */
    public function attributes()
    {
        return [
            '_id',
            'DeviceId',
            'Os',
            'Type',
            'VerOs',
            'Serial',
            'Manufacture',
            'Model',
            'SerialGsm',
            'LaunchCount',
            'DownloadCount',
            'ExitCount',
            'LastActiveTime',
            'LastIp',

            'Source',
            'Source.Site',
            'Source.Landing',
            'Source.Ad',
            'Source.Apk',
            'Source.Ver',

            'LastGeo',
            'LastGeo.countrycode',
            'LastGeo.countrycode3',
            'LastGeo.countryname',
            'LastGeo.region',
            'LastGeo.city',
            'LastGeo.postalcode',
            'LastGeo.latitude',
            'LastGeo.longitude',
            'LastGeo.metrocode',
            'LastGeo.areacode',
            'LastGeo.charset',
            'LastGeo.continentcode',

            'Loc',

            'GoogleId',
            'HasGoogleId',
            'PushClickCount',
            'PushSendedCount',

            'mail',
            'mail.LastReturnMail',

            'UpdateGoogleId',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('dict', 'Token'),
            'DeviceId' => Yii::t('dict', 'Device ID'),
            'Os' => Yii::t('dict', 'OS'),
            'Type' => Yii::t('dict', 'Type'),
            'VerOs' => Yii::t('dict', 'VerOs'),
            'Serial' => Yii::t('dict', 'Serial'),
            'Manufacture' => Yii::t('dict', 'Manufacture'),
            'Model' => Yii::t('dict', 'Model'),
            'SerialGsm' => Yii::t('dict', 'SerialGsm'),
            'LaunchCount' => Yii::t('dict', 'LaunchCount'),
            'DownloadCount' => Yii::t('dict', 'Download Count'),
            'ExitCount' => Yii::t('dict', 'Exit Count'),
            'History' => Yii::t('dict', 'History'),
            'Source' => Yii::t('dict', 'Source'),
            'LastGeo' => Yii::t('dict', 'Last Geo Info'),
            'LastIp' => Yii::t('dict', 'Last IP'),
            'Source.Site' => Yii::t('dict', 'Source Site'),
            'Source.Landing' => Yii::t('dict', 'Source Landing'),
            'Source.Ad' => Yii::t('dict', 'Source Ad'),
            'Source.Apk' => Yii::t('dict', 'Source Apk'),
            'Source.Ver' => Yii::t('dict', 'Source Version'),
            'LastGeo.countryname' => Yii::t('dict', 'Country'),
            'LastGeo.countrycode' => Yii::t('dict', 'Country'),
            'LastActiveTime' => Yii::t('dict', 'Last active time'),
            'PushClickCount' => Yii::t('dict', 'Push Click Count'),
            'PushSendedCount' => Yii::t('dict', 'Push Sended count'),
            'HasGoogleId' => Yii::t('dict', 'Has Google Id'),
            'GoogleId' => Yii::t('dict', 'Google Id'),
            'UpdateGoogleId' => Yii::t('dict', 'How update googleId'),
            'Loc' => Yii::t('dict', 'Location'),
        ];
    }

    public function getEventProvider()
    {
        return DeviceEvent::getProvider(DeviceEvent::getQuery($this->_id));
    }
}
