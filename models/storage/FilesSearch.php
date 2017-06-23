<?php

namespace app\models\storage;

use Yii;
use yii\data\ActiveDataProvider;

class FilesSearch extends Files
{
    public $CreationDateFrom;
    public $CreationDateTo;

    public function attributes()
    {
        return [
            "_id",
            'List',
            "List.Ext",
            "List.H",
            "List.W",
            "List.VideoBitrate",
            "List.AudioBitrate",
            "List.Duration",
            "List.Size",
            "StorageId",
            "CreationDate",
            "Projects"
        ];
    }

    /**
     * @inheritdoc
     */
    public function init() {
        $this->{'List.Ext'} = 'all';
        $this->{'List.H'} = 'all';
        $this->StorageId = 'all';
        $this->Projects = 'all';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [["List.Ext", "List.H", "_id","Projects"], 'string'],
            [["List.W"], 'integer'],
            [['StorageId', "CreationDateTo", "CreationDateFrom"], 'safe']
        ];
    }

    public function formName()
    {
        return "f";
    }

    public function search($params)
    {
        $query = Files::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    "_id",
                    "List.Ext",
                    "List.H",
                    "List.W",
                    "List.VideoBitrate",
                    "List.AudioBitrate",
                    "List.Duration",
                    "List.Size",
                    "List.TotalSize",
                    "StorageId",
                    "CreationDate",
                    "Size"
                ],
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

        $query->andFilterWhere(['_id' => $this->_id]);
        if ($this->{'List.Ext'} != 'all') {
            $query->andWhere(['List.Ext' => $this->{'List.Ext'}]);
        }
        if ($this->{'List.H'} != 'all') {
            switch($this->{'List.H'}) {
                case 'small' :
                    $query->andWhere(['List.H' => ['$lte' => 320]]);
                    break;
                case 'medium' :
                    $query->andWhere(['List.H' => ['$lte' => 480]]);
                    $query->andWhere(['List.H' => ['$gt' => 320]]);
                    break;
                case 'large' :
                    $query->andWhere(['List.H' => ['$lte' => 720]]);
                    $query->andWhere(['List.H' => ['$gt' => 480]]);
                    break;
                case 'huge' :
                    $query->andWhere(['List.H' => ['$gt' => 720]]);
                    break;
            }
        }
        if (!empty($this->{'List.W'})) {
            $query->andFilterWhere(['List.W' => intval($this->{'List.W'})]);
        }

        if (!empty($this->CreationDateFrom) && !empty($this->CreationDateTo)) {
            $query->andFilterWhere(['between','CreationDate',new \MongoDate($this->CreationDateFrom), new \MongoDate($this->CreationDateTo)]);
        }

        if ($this->StorageId != 'all') {
            $query->andWhere(['StorageId' => intval($this->StorageId)]);
        }

        if ($this->Projects != 'all') {
            $query->andFilterWhere(['Projects' => $this->Projects]);
        }


        return $dataProvider;
    }}