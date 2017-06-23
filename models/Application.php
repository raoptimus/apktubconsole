<?php

namespace app\models;

use app\components\MongoActiveRecord;
use Yii;

/**
 * This is the model class for table "Application".
 *
 * @property string $id
 * @property \MongoId $_id
 * @property string $Name
 * @property string $Ver
 * @property integer $BuildVer
 * @property string $Description
 * @property integer $Status
 * @property \MongoDate ReleaseDate
 */
class Application extends MongoActiveRecord
{
    public static function getDb()
    {
        return \Yii::$app->get('mongodbUpdate');
    }

    /**
     * @return string the name of the index associated with this ActiveRecord class.
     */
    public static function collectionName()
    {
        return 'Application';
    }

    /**
     * @return \MongoGridFSFile|null
     * @throws \yii\mongodb\Exception
     */
    public function getFile() {
        return $this->getDb()->getFileCollection()->get($this->_id);
    }

    public function getId() {
        return (string)$this->_id;
    }

    /**
     * @return array list of attribute names.
     */
    public function attributes()
    {
        return ['_id', 'Name', 'Ver', 'BuildVer', 'Description','Status','AddedDate','ReleaseDate'];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Application';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
/*            [['_id', 'Name', 'Ver', 'BuildVer', 'Description', 'Status'], 'required'],
            [['BuildVer', 'Status'], 'integer'],
            [['ReleaseDate'],'safe'],*/
            [['_id', 'Description', 'Status'], 'required'],
            [['Status'], 'integer'],
            [['ReleaseDate'],'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('dict', 'ID'),
            'Name' => Yii::t('dict', 'Name'),
            'Ver' => Yii::t('dict', 'Ver'),
            'BuildVer' => Yii::t('dict', 'Build Ver'),
            'Description' => Yii::t('dict', 'Description'),
            'Status' => Yii::t('dict', 'Status'),
            'AddedDate' => Yii::t('dict', 'Added date'),
            'ReleaseDate' => Yii::t('dict', 'Release date'),
        ];
    }

    public function remove() {
        //Внимание!
        // Если ты зашёл сюда из-за того, что количество чанков у тебя
        // не снижается не смотря на уничтожение приложения,
        // увеличь следующее значение:
        // расшибли лоб здесь 1 человек
        // количесто чанков по-ходу статичное, и не зависит от пользователя.
        // т.е. после уничтожения 8ми чанков - создаются ещё 8 (пустых пока что) новых
        return $this->getDb()->getFileCollection()->delete($this->_id) && $this->delete();
    }

    public function beforeSave($insert) {
        $this->Status = intval($this->Status);
        return parent::beforeSave($insert);
    }
}
