<?php

namespace app\models\stat;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;

/**
 * DailyStatSearch represents the model behind the search form about `app\models\DailyStat`.
 */
class DailyStatSearch extends DailyStat
{
    public $By;
    public $DateRange;

    public function init()
    {
        $this->DateRange = date("Y-m-d", time() - 7 * 86400) . ' - ' . date("Y-m-d");
        $this->By = "Date";
    }

    public function getDictionaries()
    {
        $this->getSiteList();
        if (Yii::$app->user->can('beManager')) {
            $this->getAdList();
            $this->getApkList();
            $this->getLandingList();
            $this->getVerList();
            $this->getPartnerList();
        }
    }

    public function getApkList()
    {
        return $this->getDistinct("Apk");
    }

    public function getSiteList()
    {
        $condition = Yii::$app->user->can('bePartner')
            ? ['Partner' => Yii::$app->user->identity->username]
            : [];

        return $this->getDistinct("Site", $condition);
    }

    public function getLandingList()
    {
        return $this->getDistinct("Landing");
    }

    public function getAdList()
    {
        return $this->getDistinct("Ad");
    }

    public function getVerList()
    {
        return $this->getDistinct("Ver");
    }

    public function getPartnerList()
    {
        return $this->getDistinct("Partner");
    }

    private function getDistinct($groupName, $condition = []) {
        $distinctList = DailyStat::getCollection()->distinct($groupName, $condition);
        $returnList = [];

        if (empty($distinctList)) {
            return $returnList;
        }
        foreach ($distinctList as $v) {
            if (empty($v)) {
                $returnList[-1] = 'Не указано';
                continue;
            }
            $returnList[strval($v)] = $v;
        }

        return $returnList;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                'DateRange',
                'string',
                'on' => 'search',
            ],
            [
                'By',
                'in',
                'range' => ['Date', 'Site', 'Landing', 'Ad', 'Apk', 'Ver', 'All', 'Partner'],
                'message' => Yii::t('dict', 'Field is invalid.'),
                'on' => 'search',
            ],
            [
                $this->attributes(), 'safe',
            ]
        ];
    }

    public function formName()
    {
        return "f";
    }

    /**
     * @return ArrayDataProvider
     * @throws \yii\mongodb\Exception
     */
    public function getDataProvider()
    {
        $match = $this->getMatch();

        $group = ['_id' => '$' . $this->By];

        $counters = $this->visibleAttributes();
        foreach ($counters as $c) {
            $group[$c] = ['$sum' => '$' . $c];
        }

        $sort = ['_id' => -1];

        $result = DailyStat::findAggregate($match, $group, $sort);

        return new ArrayDataProvider([
            'allModels' => $result,
            'sort' => [
                'attributes' => $counters,
            ],
            'pagination' => [
                'pageSize' => Yii::$app->params['statPageSize'],
            ],
        ]);
    }

    public function getMatch()
    {
        $dateRange = explode(' - ', $this->DateRange);

        $returnArray = [
            'Date' => [
                '$gte' => new \MongoDate(strtotime($dateRange[0])),
                '$lte' => new \MongoDate(strtotime($dateRange[1]))
            ],
        ];
        if (!empty($this->Site) && $this->Site != 'all') {
            if ($this->Site >= 0) {
                $returnArray['Site'] = strval($this->Site);
            } else {
                $returnArray['Site'] = '';
            }
        }
        if (!empty($this->Partner) && $this->Partner != 'all') {
            if ($this->Partner >= 0) {
                $returnArray['Partner'] = strval($this->Partner);
            } else {
                $returnArray['Partner'] = '';
            }
        }

        if (Yii::$app->user->can('beManager')) {
            if (!empty($this->Apk) && $this->Apk != 'all') {
                if ($this->Apk >= 0) {
                    $returnArray['Apk'] = strval($this->Apk);
                } else {
                    $returnArray['Apk'] = '';
                }
            }
            if (!empty($this->Ver) && $this->Ver != 'all') {
                if ($this->Ver >= 0) {
                    $returnArray['Ver'] = strval($this->Ver);
                } else {
                    $returnArray['Ver'] = '';
                }
            }
            if (!empty($this->Ad) && $this->Ad != 'all') {
                if ($this->Ad >= 0) {
                    $returnArray['Ad'] = strval($this->Ad);
                } else {
                    $returnArray['Ad'] = '';
                }
            }
            if (!empty($this->Landing) && $this->Landing != 'all') {
                if ($this->Landing >= 0) {
                    $returnArray['Landing'] = strval($this->Landing);
                } else {
                    $returnArray['Landing'] = '';
                }
            }
        }
        return $returnArray;
    }
}