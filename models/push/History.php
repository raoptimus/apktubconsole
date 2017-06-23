<?php
/**
 * Created by IntelliJ IDEA.
 * User: ra
 * Date: 03.06.15
 * Time: 11:16
 */

namespace models\push;


use app\components\MongoActiveRecord;
use MongoDate;

/**
 * Class History
 * @package models\push
 * @property int _id
 * @property State State
 * @property string Error
 * @property MongoDate AddedDate
 */
class History extends MongoActiveRecord
{
    /**
     * override methods
     */
}