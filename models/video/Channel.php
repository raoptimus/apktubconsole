<?php
namespace app\models\video;

use app\components\MongoActiveRecord;
use Yii;

/**
 *
 * @property int $_id
 * @property string $Title
 * */
class Channel extends MongoActiveRecord
{
    /**
     * @return string the name of the index associated with this ActiveRecord class.
     */
    public static function collectionName()
    {
        return 'VideoChannel';
    }

    public function formName()
    {
        return "ch";
    }

    /**
     * @return array list of attribute names.
     */
    public function attributes()
    {
        return [
            '_id',
            'Title',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Title'], 'required'],
            [['Title'], 'string'],
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
            'Title' => Yii::t('dict', 'Title'),
        ];
    }

/*    public function beforeSave($insert) {
        if ($insert) {
            $tmp_channel = Channel::find()->orderBy(['_id' => -1])->one();
            $this->_id = empty($tmp_channel) ? 1 : $tmp_channel->_id + 1;
        }
        return parent::beforeSave($insert);
    }*/

    public function init()
    {
        $this->_id = 0;
        return parent::init();
    }


}