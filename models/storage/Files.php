<?php

namespace app\models\storage;


use app\components\MongoActiveRecord;
use Yii;

/**
 * @property int _id
 * @property array List
 * @property array Projects
 * @property int StorageId
 * @property int Size
 * @property \MongoDate CreationDate
 */
class Files extends MongoActiveRecord
{
    public static function getDb()
    {
        return \Yii::$app->get('mongodb3');
    }

    public static function humanFilesize($bytes, $decimals = 2)
    {
        $sz = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }

    public static function getWeight()
    {
        $files = new Files();
        $weightArray = $files->getCollection()->aggregate(
            [
                [
                    '$unwind' => '$List'
                ],
                [
                    '$group' => [
                        '_id' => 1,
                        'overallSize' => [
                            '$sum' => '$List.Size'
                        ]
                    ]
                ]
            ]
        );
        if (isset($weightArray[0])) {
            return $weightArray[0]['overallSize'];
        }
        return 0;
    }

    public static function getCounts()
    {
        $files = new Files();
        $countsArray = $files->getCollection()->aggregate(
            [
                [
                    '$group' => [
                        '_id' => 1,
                        'countList' => [
                            '$sum' => '$TotalFiles'
                        ],
                        'count' => [
                            '$sum' => 1
                        ]
                    ]
                ]
            ]
        );

        if (isset($countsArray[0])) {
            return [
                'videos' => $countsArray[0]['count'],
                'files' => $countsArray[0]['countList'],
            ];
        }

        return [
            'videos' => 0,
            'files' => 0,
        ];
    }

    /**
     * @return string the name of the index associated with this ActiveRecord class.
     */
    public static function collectionName()
    {
        return 'Files';
    }

    public static function getProjects()
    {
        $returnArray = [];
        foreach (Yii::$app->params['projects'] as $project) {
            $returnArray[$project] = $project;
        }
        return $returnArray;
    }

    public function attributes()
    {
        return [
            "_id",
            "List",
            "StorageId",
            "CreationDate",
            "Size",
            "Projects"
        ];
    }

    public function getId()
    {
        return (string)$this->_id;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [["StorageId"], 'integer'],
            [["CreationDate", "_id", 'List'], 'safe']
        ];
    }

    public function getCreationDate()
    {
        return $this->CreationDate->sec;
    }

    /**
     * @return string
     */
    public function getExts()
    {
        return implode(' | ', $this->getAllUniqParams('Ext'));
    }

    public function getAllUniqParams($param)
    {
        $tmpArray = [];
        $this->List = $this->List ?: [];

        foreach ($this->List as $file) {
            if (isset($file[$param])) {
                $tmpArray[] = $file[$param];
            }
        }
        return array_filter(array_unique($tmpArray));
    }

    /**
     * @return string
     */
    public function getHs()
    {
        return implode(' | ', $this->getAllUniqParams('H'));
    }

    /**
     * @return string
     */
    public function getWs()
    {
        return implode(' | ', $this->getAllUniqParams('W'));
    }

    /**
     * @return string
     */
    public function getVideoBitrates()
    {
        return implode(' | ', array_map(function ($el) {
            return number_format($el / 1024, 2, '.', '') . ' кб/с';
        }, $this->getAllUniqParams('VideoBitrate')));
    }

    /**
     * @return string
     */
    public function getAudioBitrates()
    {
        return implode(' | ', array_map(function ($el) {
            return number_format($el / 1024, 2, '.', '') . ' кб/с';
        }, $this->getAllUniqParams('AudioBitrate')));
    }

    /**
     * @return string
     */
    public function getDurations()
    {
        return implode(' | ', array_map(function ($el) {
            return gmdate("H:i:s", $el);
        }, $this->getAllUniqParams('Duration')));
    }

    /**
     * @return string
     */
    public function getSizes()
    {
//        return implode(' | ', array_map(function ($el) {return number_format($el/1048576, 2, '.', '') . ' Мб';},$this->getAllUniqParams('Size')));
        return $this->getListParam('Size');
    }

    public function getListParam($param)
    {
        $tmpArray = [];
        foreach ($this->List as $file) {
            if (isset($file[$param])) {
                $tmpArray[] = $file[$param];
            }
        }
        return array_filter($tmpArray);
    }

    public function getStorage()
    {
        static $storageList;

        if (!$storageList) {
            $storageList = Storage::find()->all();
        }
        foreach ($storageList as $s) {
            if ($s->_id == $this->StorageId) {
                return $s;
            }
        }
        return null;
    }

    /**
     * @return string
     */
    public function getProjectsString()
    {
        if (is_array($this->Projects)) {
            return implode(', ', $this->Projects);
        }
        return $this->Projects;
    }
}