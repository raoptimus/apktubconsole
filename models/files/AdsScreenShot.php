<?php
namespace app\models\files;

use yii\mongodb\file\ActiveRecord;

/**
 * Class AdsScreenShot
 * @property \MongoId $_id MongoId
 * @property string $id MongoId
 * @property array $filename
 * @property string $uploadDate
 * @property string $length
 * @property string $chunkSize
 * @property string $md5
 * @property \MongoGridFSFile $file
 * @property string $newFileContent
 * Must be application/pdf, image/png, image/gif etc...
 * @property string $contentType
 * @property string $size
 */
class AdsScreenShot extends ActiveRecord
{
    public static function getDb()
    {
        return \Yii::$app->get('mongodb2');
    }

    public static function collectionName()
    {
        return 'AdsScreenShot';
    }

    public function attributes()
    {
        return array_merge(
            parent::attributes(),
            ['contentType', 'size']
        );
    }

    public function getId()
    {
        return (string)$this->_id;
    }
}