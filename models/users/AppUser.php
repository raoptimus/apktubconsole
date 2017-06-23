<?php
/**
 * Created by PhpStorm.
 * User: sainomori
 */

namespace app\models\users;

use app\components\MongoActiveRecord;
use Yii;

/**
 * @property \MongoDate CreationDate
 * @property string UserName
 * @property Device[] Devices
 * @property string Email
 * @property string[] Tokens
 * @property array Premium
 */
class AppUser extends MongoActiveRecord
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
        return 'User';
    }

    /**
     * @return array list of attribute names.
     */
    public function attributes()
    {
        return [
            '_id',
            'UserName',
            'Tokens',
            'Tel',
            'Email',
            'Language',
            'Premium',
            'Premium.Type',
            'Premium.Expires',
            'CreationDate',
            'mail',
            'mail.welcomeSended',
            'mail.LastReturnMail',
            'mail.Subscribe'
        ];
    }

    public function afterFind()
    {
        if (empty($this->Premium)) {
            $this->Premium = ['Expires' => new \MongoDate(0), 'Type' => 'none'];
        }

        parent::afterFind();
    }

    public function isPremiumExpires()
    {
        return $this->getPremiumExpires()->sec < time();
    }

    public function getPremiumExpires()
    {
        return @$this->Premium['Expires'] ?: new \MongoDate(0);
    }

    public function getPremiumType()
    {
        return @$this->Premium['Type'] ?: 'none';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => "Id",
            'UserName' => Yii::t('dict', 'User name'),
            'Tokens' => Yii::t('dict', 'Tokens'),
            'Tel' => Yii::t('dict', 'User phone'),
            'Email' => Yii::t('dict', 'User email'),
            'Language' => Yii::t('dict', 'Language'),
            'PremiumExpires' => Yii::t('dict', 'Premium expires'),
            'PremiumType' => Yii::t('dict', 'Premium type'),
            'Premium' => Yii::t('dict', 'Premium'),
            'CreationDate' => Yii::t('dict', 'Creation date'),
        ];
    }

    /**
     * @return Device[]
     */
    public function getDevices()
    {
        return Device::find()->where(['_id' => $this->Tokens])->all();
    }
}