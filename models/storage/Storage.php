<?php

namespace app\models\storage {

    use app\components\MongoActiveRecord;
    use Yii;

    /**
     * Class Storage
     * @package models\storage
     * @property int _id
     * @property string Title
     * @property string StorageType
     * @property string Username
     * @property string Password
     * @property string Tenant
     * @property string ApiKey
     * @property string Domain
     * @property int DomainId
     * @property string AuthUrl
     * @property string Container
     * @property int Port
     * @property int UsedSpace
     * @property int TotalFiles
     * @property \MongoDate CreationDate
     */
    class Storage extends MongoActiveRecord
    {
        public static function getDb()
        {
            return \Yii::$app->get('mongodb3');
        }

        /**
         * @return string the name of the index associated with this ActiveRecord class.
         */
        public static function collectionName()
        {
            return 'Storage';
        }

        public static function getList()
        {
            $storages = self::find()->all();
            $returnArray = [];
            foreach ($storages as $storage) {
                $returnArray[$storage->_id] = $storage->Title;
            }
            return $returnArray;
        }

        public static function getValue($key)
        {
            $storage = self::findOne(['_id' => intval($key)]);
            if ($storage) {
                return $storage->Title;
            }
            return $key;
        }

        public function attributes()
        {
            return [
                "_id",
                "Title",
                "StorageType",
                "Username",
                "Password",
                "Tenant",
                "ApiKey",
                "Domain",
                "DomainId",
                "AuthUrl",
                "Container",
                "Port",
                "UsedSpace",
                "TotalFiles",
                "CreationDate",
            ];
        }

        /**
         * @inheritdoc
         */
        public function attributeLabels()
        {
            return [
                '_id' => Yii::t('dict', 'Id'),
                'Title' => Yii::t('dict', 'Title'),
                'StorageType' => Yii::t('dict', 'Storage Type'),
                'Username' => Yii::t('dict', 'Username'),
                'Password' => Yii::t('dict', 'Password'),
                'Tenant' => Yii::t('dict', 'Tenant'),
                'ApiKey' => Yii::t('dict', 'Api Key'),
                'Domain' => Yii::t('dict', 'Domain'),
                'DomainId' => Yii::t('dict', 'Domain Id'),
                'AuthUrl' => Yii::t('dict', 'Auth Url'),
                'Container' => Yii::t('dict', 'Container'),
                'Port' => Yii::t('dict', 'Port'),
                'UsedSpace' => Yii::t('dict', 'Used Space'),
                'TotalFiles' => Yii::t('dict', 'Total Files'),
                'CreationDate' => Yii::t('dict', 'Creation Date'),
            ];
        }

        /**
         * @inheritdoc
         */
        public function rules()
        {
            return [
                [["Title", 'StorageType', 'Username'], 'required'],
                [["Title", 'Username', "Password", 'Tenant', 'ApiKey', 'Domain', 'AuthUrl', 'Container', "DomainId"], 'string'],
                [
                    'StorageType',
                    'in',
                    'range' => ['swift', 'ftp', 'rsync'],
                ],

                [['Port', 'UsedSpace', 'TotalFiles'], 'integer'],
                [["_id", "CreationDate"], 'safe'],
            ];
        }

        public function getId()
        {
            return (string)$this->_id;
        }

        public function beforeSave($insert)
        {
            $this->DomainId = intval($this->DomainId);
            $this->Port = intval($this->Port);
            $this->TotalFiles = intval($this->TotalFiles);
            $this->UsedSpace = intval($this->UsedSpace);

            if ($insert) {
                $this->CreationDate = new \MongoDate();
            }
            return parent::beforeSave($insert);
        }

        public function init()
        {
            $this->_id = 0;
        }

        public function getCreationDate()
        {
            return $this->CreationDate->sec;
        }

        public function beforeDelete()
        {
            if ($this->TotalFiles > 0 && $this->UsedSpace > 0) {
                return false;
            }
            return parent::beforeDelete();
        }
    }
}