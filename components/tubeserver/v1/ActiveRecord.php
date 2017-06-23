<?php

namespace app\components\tubeserver\v1;


use Yii;
use yii\base\NotSupportedException;
use yii\db\BaseActiveRecord;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

class ActiveRecord extends BaseActiveRecord
{

    /**
     * Returns the primary key **name(s)** for this AR class.
     * Note that an array should be returned even when the record only has a single primary key.
     * For the primary key **value** see [[getPrimaryKey()]] instead.
     * @return string[] the primary key name(s) for this AR class.
     */
    public static function primaryKey()
    {
        return ['Id'];
    }

    public static function find()
    {
        return Yii::createObject(ActiveQuery::className(), [get_called_class()]);
    }

    public function delete()
    {
        throw new NotSupportedException("Method is not implemented");
    }

    public static function deleteAll($condition = '', $params = [])
    {
        throw new NotSupportedException("Method is not implemented");
    }

    public function update($runValidation = true, $attributeNames = null)
    {
        throw new NotSupportedException("Method is not implemented");
    }

    public static function updateAll($attributes, $condition = '')
    {
        throw new NotSupportedException("Method is not implemented");
    }

    public function insert($runValidation = true, $attributes = null)
    {
        throw new NotSupportedException("Method is not implemented");
    }

    /**
     * Returns the connection used by this AR class.
     * @return Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return \Yii::$app->get('jsonrpc');
    }

    /**
     * @return string
     */
    public static function getControllerName()
    {
        return Inflector::camel2id(StringHelper::basename(get_called_class()), '_') . "Controller";
    }
}