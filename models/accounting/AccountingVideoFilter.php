<?php

namespace app\models\accounting;

use app\components\MongoActiveRecord;
use Yii;
use app\models\Journal;
use app\models\User;
use yii\data\ArrayDataProvider;


/**
 * Aggregate approved videos
 *
 */
class AccountingVideoFilter extends MongoActiveRecord
{

    public $DateRange;
    public $Author;

    public function init()
    {
        $this->DateRange = date("Y-m-d", time() - 7 * 86400) . ' - ' . date("Y-m-d");
    }

    public function formName()
    {
        return "f";
    }

    public function setFilter(array $aFilter = null)
    {
        if (! is_array($aFilter)) {
            return;
        }
        foreach ($aFilter as $field => $value) {
            if ($value and in_array($field, ['DateRange', 'Author'])) {
                $this->{$field} = $aFilter[ $field ];
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'DateRange' => Yii::t('dict', 'Date'),
            'Author' => Yii::t('dict', 'User'),
        ];
    }

    /**
     * @return ArrayDataProvider
     * @throws \yii\mongodb\Exception
     */
    public function getDataProvider()
    {
        return new ArrayDataProvider([
            'allModels' => $this->getData(),
            'pagination' => [
                'pageSize' => Yii::$app->params['statPageSize'],
            ],
        ]);
    }

    private function getData()
    {
        $aggregate = [];

        $dataset = Journal::find()
            ->where($this->getMatch())
            ->all();

        foreach ($dataset as $entry) {
            $time = $entry->AddedDate->sec;
            $dateKey = date('Y-m-', $time) . (date('j', $time) <= 16 ? '01' : '17');

            if (! isset($aggregate[ $dateKey ][ $entry->UserId ])) {
                $aggregate[ $dateKey ][ $entry->UserId ] = 0;
            }
            $aggregate[ $dateKey ][ $entry->UserId ]++;
        }

        if (empty($aggregate)) {
            return [];
        }

        ksort($aggregate);

        $result = [];
        foreach ($aggregate as $period => $periodData) {
            foreach ($periodData as $userId => $ApproveCount)
            $result[] = [
                'Period' => $period,
                'UserId' => $userId,
                'ApproveCount' => $ApproveCount,
            ];
        }
        return $result;
    }

    private function getMatch()
    {
        $dateRange = explode(' - ', $this->DateRange);

        $returnArray = [
            'AddedDate' => [
                '$gte' => new \MongoDate(strtotime($dateRange[0])),
                '$lte' => new \MongoDate(strtotime($dateRange[1])),
            ],
            'Operation' => 'VideoPublished'
        ];

        if ($this->Author) {
            $returnArray['UserId'] = User::getIdByName($this->Author);
        }

        return $returnArray;
    }
}
