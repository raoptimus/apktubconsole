<?php
namespace app\models\push;

use app\components\MongoActiveRecord;

/**
 * Class NotifyToken
 * @property string Token
 * @package app\models\push
 */
class NotifyToken extends MongoActiveRecord
{
    public function rules()
    {
        return [
            [
                'Token',
                'string',
                'min' => 32,
                'max' => 32,
                'length' => 32,
            ],
            [
                'Token',
                'required',
            ]
        ];
    }

    public function attributes()
    {
        return [
            "Token",
        ];
    }

}