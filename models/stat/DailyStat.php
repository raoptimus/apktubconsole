<?php

namespace app\models\stat;

use app\components\MongoActiveRecord;
use Yii;

/**
 * This is the model class for table "DailyStat".
 * @property string _id
 */
class DailyStat extends MongoActiveRecord
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
        return 'DailyStat';
    }

    /**
     * @return array list of attribute names.
     */
    public function attributes()
    {
        return array_merge([
            '_id',
            'Date',
            'Site',
            'Landing',
            'Ad',
            'Apk',
            'Ver',
            'Partner'
        ], $this->visibleAttributes());
    }

    public function visibleAttributes() {
        $returnArray = ['RegCount'];

        if (Yii::$app->user->can('beManager')) {
            $returnArray = array_merge($returnArray,[
                'ReinstallCount',
                'UpgradeCount',
                'ULaunchCount',
                'TLaunchCount',

                'LikeVideoCount',
                'ViewVideoCount',
                'DownloadVideoCount',
                'VideoCommentCount',

                'PushSendedCount',
                'PushClickCount',

                'PremiumSignupCount',
                'PremiumRebillCount',
                'PremiumRefundCount',
            ]);
        }
        return $returnArray;
    }

    public function afterFind()
    {
        if ($this->_id instanceof \MongoDate) {
            $this->_id = date("d-m-Y", $this->_id->sec);
        }
        return parent::afterFind();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => "Id",
            'Date' => Yii::t('dict', 'Date'),
            'RegCount' => Yii::t('dict', 'Registers'),

            'ReinstallCount' => Yii::t('dict', 'Reinstall'),
            'UpgradeCount' => Yii::t('dict', 'Upgrade'),
            'ULaunchCount' => Yii::t('dict', 'Unique launch'),
            'TLaunchCount' => Yii::t('dict', 'Total launch'),

            'LikeVideoCount' => Yii::t('dict', 'Like video'),
            'ViewVideoCount' => Yii::t('dict', 'View video'),
            'DownloadVideoCount' => Yii::t('dict', 'Download video'),
            'FLaunchCount' => Yii::t('dict', 'First launch'),
            'VideoCommentCount' => Yii::t('dict', 'Video comment'),

            'PushSendedCount' => Yii::t('dict', 'Push sended'),
            'PushClickCount' => Yii::t('dict', 'Push click'),

            'PremiumSignupCount' => Yii::t('dict', 'Premium signups'),
            'PremiumRebillCount' => Yii::t('dict', 'Premium rebills'),
            'PremiumRefundCount' => Yii::t('dict', 'Premium refunds'),
        ];
    }
}
