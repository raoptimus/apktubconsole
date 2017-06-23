<?php

namespace app\models;

use Yii;
use yii\mongodb\ActiveRecord;

/**
 * This is the model class for table "Application".
 *
 * @property string $_id
 * @property string $email
 * @property string $date
 * @property string $type
 */
class MailStat extends ActiveRecord
{
    /**
     * @return string the name of the index associated with this ActiveRecord class.
     */
    public static function collectionName()
    {
        return 'MailStat';
    }

    /**
     * @return array list of attribute names.
     */
    public function attributes()
    {
        return ['_id', 'email', 'date','type'];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'date','type'], 'required'],
            [['_id'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('dict', 'ID'),
            'email' => Yii::t('dict', 'Email'),
            'date' => Yii::t('dict', 'Date'),
        ];
    }
}
