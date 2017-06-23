<?php

namespace app\models\video;

use app\components\MongoActiveRecord;
use app\models\users\AppUser;
use MongoDate;
use Yii;

/**
 *
 * @property int $_id
 * @property int $VideoId
 * @property int $UserId
 * @property string $Body
 * @property int $Status
 * @property MongoDate $PostDate
 * @property string $Language
 */
class Comment extends MongoActiveRecord
{
    public $VideoIdTitle = '';
    public $UserIdTitle = '';
    /**
     * @return string the name of the index associated with this ActiveRecord class.
     */
    public static function collectionName()
    {
        return 'VideoComment';
    }

    /**
     * @return array list of attribute names.
     */
    public function attributes()
    {
        return [
            '_id',
            'VideoId',
            'UserId',
            'Body',
            'Status',
            'PostDate',
            'Language',
        ];
    }
    public function formName()
    {
        return "v";
    }

    /**
     * @inheritdoc
     */
/*    public function rules()
    {
        return [
            [['_id', 'Duration', 'CategoryId', 'PublishedDate', 'UpdateDate', 'Title', 'Desc'], 'required'],
            [['DescForm', 'TitleForm','TagsForm'],'safe'],
//            ['Title', 'app\components\EmbedDocListValidator', 'model' => 'app\models\Text'],
//            ['Source', 'app\components\EmbedDocValidator', 'model' => 'app\models\video\VideoSource'],
        ];
    }
*/
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('dict', 'Id'),
            'VideoId' => Yii::t('dict', 'Video ID'),
            'UserId' => Yii::t('dict', 'User ID'),
            'Body' => Yii::t('dict', 'Comment body'),
            'Status' => Yii::t('dict', 'Status'),
            'PostDate' => Yii::t('dict', 'Post Date'),
            'Language' => Yii::t('dict', 'Language'),
        ];
    }

    public static function getVideoIdTitles($idArray = []) {
        $models = Video::find()->where(['_id'=>$idArray])->all();
        $returnArray = [];
        foreach ($models as $model) {
            $returnArray[$model->_id] = $model->getLangTitle('ru');
        }
        return $returnArray;
    }

    public static function getUserIdTitles($idArray = []) {
        $models = AppUser::find()->where(['_id'=>$idArray])->all();
        $returnArray = [];
        foreach ($models as $model) {
            $returnArray[$model->_id] = $model->UserName;
        }
        return $returnArray;
    }

    public function spam() {
        $this->Status = 1;
        $video = Video::findOne(['_id' => intval($this->VideoId)]);
        return $this->save() && $video->spamComment($this->_id);
    }

    public function remove() {
        $video = Video::findOne(['_id' => intval($this->VideoId)]);
        return $this->delete() && $video->deleteComment($this->_id);
    }

}