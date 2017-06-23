<?php
namespace app\models\video;

use app\components\MongoActiveRecord;
use Yii;

/**
 *
 * @property int $_id
 * @property string $Name
 * */
class Actor extends MongoActiveRecord
{
    /**
     * @return string the name of the index associated with this ActiveRecord class.
     */
    public static function collectionName()
    {
        return 'Actor';
    }

    public function formName()
    {
        return "a";
    }

    /**
     * @return array list of attribute names.
     */
    public function attributes()
    {
        return [
            '_id',
            'Name',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Name'], 'required'],
            [['Name'], 'string'],
            [['_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('dict', 'Id'),
            'Name' => Yii::t('dict', 'Name'),
        ];
    }

    public function init()
    {
        $this->_id = 0;
        return parent::init();
    }
}