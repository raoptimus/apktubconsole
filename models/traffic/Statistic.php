<?php

namespace app\models\traffic;

use Yii;
use app\components\MongoActiveRecord;

/**
 * This is the model class for table "Application".
 *
 * @property string $_id
 * @property string $name
 * @property string $date
 * @property string $monthStart
 * @property string $monthEnd;
 * @property string $statistic
 * @property string $type
 */
class Statistic extends MongoActiveRecord
{
    public static function getDb()
    {
        return \Yii::$app->get('authDb');
    }

    /**
     * @return string the name of the index associated with this ActiveRecord class.
     */
    public static function collectionName()
    {
        return 'TrafficStatistic';
    }

    /**
     * @return array list of attribute names.
     */
    public function attributes()
    {
        return ['_id', 'statistic', 'date', 'ga', 'name', 'monthStart', 'monthEnd', 'type'];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['_id'], 'string']
        ];
    }

    /**
     * Получаем статистку напрямую так как засрос сложноватый
     */
    public static function getStatistic($monthStart = null, $monthEnd = null)
    {
        self::getCollection()->createIndex(['monthStart' => 1]);

        $aggreagtion = [
            [ '$unwind'=> '$statistic' ],
            [ '$sort'=> [ '_id'=> 1, 'statistic.data.desktop.users'=> -1 ] ],
            [ '$group'=> [
                '_id'=> '$_id',
                'monthStart' => [ '$first'=> '$monthStart' ],
                'monthEnd' => [ '$first'=> '$monthEnd' ],
                'date' => [ '$first'=> '$date' ],
                'statistic'=> [ '$push'=> '$statistic' ]
            ]],
            ['$sort'=> [ 'monthStart'=>1]],
            [
                '$match' => [
                    'monthStart' =>
                        ['$gte' =>  $monthStart, '$lte' =>  $monthEnd]
                ]
            ]
        ];



        return  self::getCollection()->aggregate($aggreagtion);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => '_id',
            'date' => 'date',
        ];
    }
}
