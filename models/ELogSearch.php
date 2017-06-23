<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;

class ELogSearch extends ELog
{
    public $DateFrom;
    public $DateTo;
    public $DateRange;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [["_id", "Priority", "Hostname", "Tag", "Msg"], 'string'],
            [["Pid"], 'integer'],
            [["DateFrom","DateTo","DateRange"], 'safe']
        ];
    }

    public function init() {
        $this->Priority = 'all';
        $this->DateFrom = mktime(0, 0, 0, date("m")  , date("d")-1, date("Y"));
        $this->DateTo = mktime(23, 59, 59, date("m")  , date("d"), date("Y"));
    }

    public function formName()
    {
        return "el";
    }

    public function search($params)
    {
        $query = ELog::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    '_id'=> SORT_DESC
                ]
            ],
            'pagination' => [
                'pageSize' => Yii::$app->params['appUserPageSize'],
            ],
        ]);

        $this->load($params);
        if (!empty($params['date_range'])) {
            $dateRange = explode(' - ',$params['date_range']);
            if (count($dateRange) == 2) {
                $dateFrom = explode('-',$dateRange[0]);
                if (count($dateFrom) == 3) {
                    $this->DateFrom = mktime(0, 0, 0, $dateFrom[1]  , $dateFrom[2], $dateFrom[0]);
                }
                $dateTo = explode('-',$dateRange[1]);
                if (count($dateTo) == 3) {
                    $this->DateTo = mktime(23, 59, 59, $dateTo[1]  , $dateTo[2], $dateTo[0]);
                }
            }
        }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['_id' => $this->_id])
            ->andFilterWhere(['between','Time',new \MongoDate($this->DateFrom), new \MongoDate($this->DateTo)])
            ->andFilterWhere(['like', 'Hostname', $this->Hostname])
            ->andFilterWhere(['like', 'Tag', $this->Tag])
            ->andFilterWhere(['like', 'Msg', $this->Msg]);

        if ($this->Priority != 'all') {
            $query->andWhere(['Priority' => intval($this->Priority)]);
        }


        return $dataProvider;
    }}