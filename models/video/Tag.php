<?php

namespace app\models\video;

use app\components\MongoActiveRecord;
use app\models\Language;
use Yii;

/**
 *
 * @property string $_id
 * @property string[] $Title
 * @property int $VideoCount
 * @property string $FormTitle
 */
class Tag extends MongoActiveRecord
{
    /**
     * @return string the name of the index associated with this ActiveRecord class.
     */
    public static function collectionName()
    {
        return 'Tag';
    }

    /**
     * @return array list of attribute names.
     */
    public function attributes()
    {
        return [
            '_id',
            'Title',
            'VideoCount',
        ];
    }

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return "v";
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->_id = 0;
        return parent::init();
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['Title','TitleArray','_id'],'safe'],
            [['VideoCount'],'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('dict', 'Id'),
            'Title' => Yii::t('dict', 'Title'),
            'VideoCount' => Yii::t('dict', 'Video count'),
        ];
    }

    public function getFormTitle($lang = 'ru') {
        foreach ($this->Title as $quote) {
            if ($quote['Language'] = $lang) {
                return $quote['Quote'];
            }
        }
        return $this->Title[0]['Quote'];
    }

    public function getTitleArray() {
        $returnArray = [];
        if (empty($this->Title)) {
            $returnArray['ru'] = Language::getValue('ru');
        } else {
            foreach ($this->Title as $Quote) {
                $returnArray[$Quote['Language']] = $Quote['Quote'];
            }
        }
        return $returnArray;
    }

    public function setTitleArray($input) {
        $input = array_filter($input);
        $clean_array = [];
        foreach ($input as $lang_key => $lang_value) {
            $clean_array[] = [
                'Language' => $lang_key,
                'Quote' => $lang_value
            ];
        }
        $this->Title = $clean_array;
    }

    public function getEmptyTitleArray() {
        $notEmptyLangs = $this->TitleArray;
        $returnArray = [];
        foreach (Language::getList() as $lang_key => $lang_value) {
            if(!isset($notEmptyLangs[$lang_key])) {
                $returnArray[$lang_key] = $lang_value;
            }
        }
        return $returnArray;
    }

    public function incVideoCount() {
        //todo
//        $this->update(false, []);
        return $this->getCollection()->update(['_id' => $this->_id], ['$inc' => ['VideoCount' => 1]]);
    }

    public function decVideoCount() {
        return $this->getCollection()->update(['_id' => $this->_id], ['$inc' => ['VideoCount' => -1]]);
    }

    public static function convertToUseful($formalTags, $tagName = 'Tags'){
        $usefulTags = [];
        foreach ($formalTags as $formalTag) {
            $usefulTags[$formalTag['Language']] = $formalTag[$tagName];
        }
        return $usefulTags;
    }

    public static function convertToFormal($usefulTags, $tagName = 'Tags'){
        $formalTags = [];
        foreach ($usefulTags as $lang => $usefulTag) {
            $formalTags[] = [
                'Language' => $lang,
                $tagName => $usefulTag
            ];
        }
    }
}