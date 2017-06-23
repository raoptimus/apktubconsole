<?php
namespace app\models\video;
use yii\mongodb\ActiveRecord;

class VideoSource extends ActiveRecord
{
    public $_id;
    public $SourceId;
    public $Domain;
    public $ScreenshotCount;
    public $ScreenshotSelectIndex;

    /**
     * @return array list of attribute names.
     */
    public function attributes()
    {
        return [
            '_id',
            'SourceId',
            'Domain',
            'ScreenshotCount',
            'ScreenshotSelectIndex',
        ];
    }

    public function rules()
    {
        return [
            [['_id', 'SourceId', 'Domain', 'ScreenshotCount', 'ScreenshotSelectIndex'], 'required'],
        ];
    }
}