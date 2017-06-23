<?php

namespace app\models\premium;

use app\components\MongoActiveRecord;
use Yii;
use yii\data\ArrayDataProvider;


/**
 * @property int _id
 * @property string Token
 * @property \MongoDate AddedDate
 * @property array Operations
 * @property array Tariffs
 */
class Stat extends MongoActiveRecord
{
    public $DateRange;
    public $Operation;
    public $Tariff;

    public static function getDb()
    {
        return \Yii::$app->get('mongodb2');
    }

    public static function getTariffTitle($id)
    {
        $tariff = Tariff::find(['_id' => intval($id)])->one();
        if ($tariff) {
            return $tariff->FormTitle;
        }
        return $id;
    }

    /**
     * @return string the name of the index associated with this ActiveRecord class.
     */
    public static function collectionName()
    {
        return 'PremiumTransaction';
    }

    public function init()
    {
        $c = self::getCollection();
        $hasIx = !empty(array_filter($c->mongoCollection->getIndexInfo(), function ($v) {
            return ($v['name'] == 'AddedDate_-1');
        }));
        if (!$hasIx) {
            self::getCollection()->createIndex(['AddedDate' => -1], ['background' => true]);
        }

        $this->DateRange = date("Y-m-d", time() - 7 * 86400) . ' - ' . date("Y-m-d");
    }

    public function attributes()
    {
        /**
         * public $_id;
         * public $TransactionId = 0;
         * public $Token;
         * public $Price = 0.0;
         * public $Duration = 0;
         * public $UserIds = [];
         * public $TariffId = 0;
         * public $BillingId = 0;
         * public $Type;
         * public $TransactionData;
         * public $Phone;
         * public $Email;
         * public $RealDate;
         * public $SubscribeId;
         * public $Test = false;
         * public $AddedDate;
         */
        return [
            "_id",
            "Token",
            "Duration",
            "UserIds",
            "TariffId",
            "BillingId",
            "Type",
            "AddedDate",
            "DisplayPrice",
            "AproxPrice",
            "Currency",
        ];
    }

    public function getOperations()
    {
        return [
            'trial' => 'Триал',
            'signup' => 'Покупка',
            'rebill' => 'Продление',
            'refund' => 'Возврат'
        ];
    }

    public function getBillings()
    {
        return [
            1 => 'Aebill',
            2 => 'Sms2ru',
        ];
    }

    public function getTariffs()
    {
        $tariffs = Tariff::find()->all();
        $ret = [];

        foreach ($tariffs as $tariff) {
            $ret[$tariff->id] = $tariff->FormTitle;
        }

        return $ret;
    }

    public function getId()
    {
        return (string)$this->_id;
    }

    public function getAddedDate()
    {
        return $this->AddedDate->sec;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    "_id",
                    "Token",
                    "Duration",
                    "DisplayPrice",
                    "AproxPrice",
                    "Currency",
                    "UserIds",
                    'TariffId',
                    'BillingId',
                    "Type",
                    "AddedDate"
                ],
                'string'
            ],
            [
                [
                    'DateRange',
                    'Operation',
                    'Tariff'
                ],
                'safe'
            ],
        ];
    }

    public function formName()
    {
        return "pt";
    }

    /**
     * @param $params
     * @return ArrayDataProvider
     * @throws \yii\mongodb\Exception
     */
    public function search($params)
    {
        $this->load($params);
        $project = self::getProject();
        $match = $this->getMatch();
        $group = self::getGroup();
        $sort = [
            '_id.year' => -1,
            '_id.month' => -1,
            '_id.day' => -1,
        ];
        $q = [
            ['$match' => $match],
            ['$project' => $project],
            ['$group' => $group],
            ['$sort' => $sort],
        ];

        if (empty($match)) {
            array_shift($q);
        }

        $result = static::getCollection()->aggregate($q);
        $items = array_map(function ($r) {
            return [
                'date' => strtotime($r['_id']['year'] . '-' . $r['_id']['month'] . '-' . $r['_id']['day']),
                'tariffId' => $r['_id']['tariff'],
                'operationCount' => $r['count'],
                'price' => $r['price'],
            ];
        }, $result);

        return new ArrayDataProvider([
            'allModels' => $items,
            'pagination' => [
                'pageSize' => Yii::$app->params['statPageSize'],
            ],
        ]);
    }

    public static function getProject()
    {
        return [
            '_id' => 1,
            'Type' => 1,
            'Currency' => 1,
            'AddedDate' => 1,
            'TariffId' => 1,
            'Price' => '$Price.approx',
            'year' => [
                '$year' => '$AddedDate'
            ],
            'month' => [
                '$month' => '$AddedDate'
            ],
            'day' => [
                '$dayOfMonth' => '$AddedDate'
            ],
        ];

    }

    public function getMatch()
    {
        $ret = [];

        if (!empty($this->DateRange)) {
            $dateRange = explode(' - ', $this->DateRange);
            $since = strtotime($dateRange[0]);
            $since = strtotime(date("Y-m-d 00:00:00", $since));
            $till = strtotime($dateRange[1]);
            $till = strtotime(date("Y-m-d 23:59:59", $till));

            $ret['AddedDate'] = [
                '$gte' => new \MongoDate($since),
                '$lte' => new \MongoDate($till),
            ];
        }

        if (!empty($this->Operation) && $this->Operation != 'all') {
            $ret['Type'] = strval($this->Operation);
        }

        if (!empty($this->Tariff) && $this->Tariff != 'all') {
            $ret['TariffId'] = intval($this->Tariff);
        }

        return $ret;
    }

    public static function getGroup()
    {
        return [
            '_id' => [
                'year' => '$year',
                'month' => '$month',
                'day' => '$day',
                'tariff' => '$TariffId'
            ],
            'count' => ['$sum' => 1],
            'price' => ['$sum' => '$Price']
        ];
    }
}