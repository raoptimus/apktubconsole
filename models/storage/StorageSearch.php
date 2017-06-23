<?php

namespace app\models\storage;

use Yii;
use yii\data\ActiveDataProvider;

class StorageSearch extends Storage
{
    public $CreationDateFrom;
    public $CreationDateTo;

    /**
     * @inheritdoc
     */
    public function init() {
        $this->StorageType = 'all';
//        $this->CreationDateFrom = mktime(0, 0, 0, date("m")  , date("d")-1, date("Y"));
//        $this->CreationDateTo = mktime(23, 59, 59, date("m")  , date("d"), date("Y"));
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [["Title",'Username',"Password",'Tenant','ApiKey','Domain','AuthUrl','Container'], 'string'],
            [
                'StorageType',
                'in',
                'range' => ['all','swift', 'ftp', 'rsync'],
            ],

            [["DomainId",'Port'/*,'UsedSpace','TotalFiles'*/], 'integer'],
            [["_id","CreationDate","CreationDateTo", "CreationDateFrom"],'safe'],
        ];
    }

    public function formName()
    {
        return "s";
    }

    public function search($params)
    {
        $query = Storage::find();

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
                    $this->CreationDateFrom = mktime(0, 0, 0, $dateFrom[1]  , $dateFrom[2], $dateFrom[0]);
                }
                $dateTo = explode('-',$dateRange[1]);
                if (count($dateTo) == 3) {
                    $this->CreationDateTo = mktime(23, 59, 59, $dateTo[1]  , $dateTo[2], $dateTo[0]);
                }
            }
        }

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'Title', $this->Title])
            ->andFilterWhere(['like', 'Username', $this->Username])
            ->andFilterWhere(['like', 'Password', $this->Password])
            ->andFilterWhere(['like', 'Tenant', $this->Tenant])
            ->andFilterWhere(['like', 'Title', $this->Title])
            ->andFilterWhere(['like', 'ApiKey', $this->ApiKey])
            ->andFilterWhere(['like', 'Domain', $this->Domain])
            ->andFilterWhere(['like', 'AuthUrl', $this->AuthUrl])
            ->andFilterWhere(['like', 'Container', $this->Container]);

        if (!empty($this->DomainId)) {
            $query->andFilterWhere(['DomainId' => intval($this->DomainId)]);
        }
        if (!empty($this->_id)) {
            $query->andFilterWhere(['_id' => intval($this->_id)]);
        }
        if (!empty($this->Port)) {
            $query->andFilterWhere(['Port' => intval($this->Port)]);
        }
        if (!empty($this->UsedSpace)) {
            $query->andFilterWhere(['UsedSpace' => intval($this->UsedSpace)]);
        }
        if (!empty($this->TotalFiles)) {
            $query->andFilterWhere(['TotalFiles' => intval($this->TotalFiles)]);
        }

        if ($this->StorageType != 'all') {
            $query->andWhere(['StorageType' => $this->StorageType]);
        }

        if (!empty($this->CreationDateFrom) && !empty($this->CreationDateTo)) {
            $query->andFilterWhere(['between', 'CreationDate', new \MongoDate($this->CreationDateFrom), new \MongoDate($this->CreationDateTo)]);
        }

        return $dataProvider;
    }}