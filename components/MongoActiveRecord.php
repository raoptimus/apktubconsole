<?php
namespace app\components;

use \yii\mongodb\ActiveRecord;

class MongoActiveRecord extends ActiveRecord
{
    public static function getDb()
    {
        $c = get_called_class();

        if (in_array($c, [
            'app\models\User',
            'app\models\AdminUserRoles',
            'app\models\traffic\Statistic'
        ])) {
            return \Yii::$app->get('authDb');
        }

        if (in_array($c, [
            //
            'app\models\Application',
            //
            //
            'app\models\users\Device',
            'app\models\users\AppUser',
            //
            'app\models\stat\DailyStat',
            //
            'app\models\push\Log',
            'app\models\push\Task',
            //
            'app\models\ELog',
            //
            'app\models\Ads',
            //
            'app\models\premium\Tariff',
            'app\models\premium\Stat',
            //
            'app\models\accounting\AccountingVideoFilter'
        ])) {
            return \Yii::$app->get('mongodb2');
        }

        if (in_array($c, [
            'app\models\storage\Files',
            'app\models\storage\Storage',
        ])) {
            return \Yii::$app->get('mongodb3');
        }

        return parent::getDb();
    }

    public function attributeHints()
    {
        return [];
    }

    public function getAttributeHint($attr)
    {
        $hints = $this->attributeHints();
        if (isset($hints[$attr])) {
            return $hints[$attr];
        }
        return "";
    }

    /**
     * @param $text
     * @return \yii\mongodb\Query
     */
    public static function findText($text)
    {
        return static::find()->
            //todo mongodb >= 3.2 $caseSensitive
            where(['$text' => ['$search' => $text/*, '$caseSensitive' => true*/]])->
            select(['score' => ['$meta' => 'textScore']])->
            orderBy(['score' => ['$meta' => 'textScore']]);
    }

    public static function findAggregate($match, $group, $sort)
    {
        $result = static::getCollection()->aggregate([
            ['$match' => $match],
            ['$group' => $group],
            ['$sort' => $sort],
        ]);

        return self::all($result);
    }

    public static function sequenceId()
    {
        $doc = static::getDb()->getCollection("Sequence")->findAndModify(
            ['_id' => static::collectionName() . '__id'],
            ['$inc' => ['lastId' => 1]],
            null,
            ['new' => true, 'upsert' => true]
        );

        return (int)$doc['lastId'];
    }

    public function beforeValidate()
    {
        $this->checkIntId();
        return parent::beforeValidate();
    }

    public function beforeInsert()
    {
        $this->checkIntId();
    }

    private function checkIntId()
    {
        if (!isset($this->_id)) {
            return;
        }
        if ($this->_id === 0) {
            $this->_id = self::sequenceId();
        }
    }

    private static function all($rows)
    {
        $className = get_called_class();
        return array_map(function($row) use($className) {
            $c = new $className;
            $c->populateRecord($c, $row);
            $c->afterFind();
            return $c;
        }, $rows);
    }
}

