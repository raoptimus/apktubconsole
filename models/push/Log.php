<?php
/**
 * Created by IntelliJ IDEA.
 * User: ra
 * Date: 22.05.15
 * Time: 22:10
 */

namespace app\models\push;


use app\components\MongoActiveRecord;

class Log extends MongoActiveRecord
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
        return 'PushLog';
    }

    public function formName()
    {
        return "pl";
    }
}