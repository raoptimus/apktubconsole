<?php
/**
 * Created by IntelliJ IDEA.
 * User: ra
 * Date: 11.05.15
 * Time: 2:52
 */

namespace app\models\video;


use app\components\MongoActiveRecord;
use app\components\Transliterator;
use app\models\Language;
use app\models\Text;
use Yii;
use yii\base\InvalidParamException;

/**
 *
 * @property int $_id
 * @property Text[] $Title
 * @property string[] $TitleForm
 * @property Text[] $Slug
 * @property string[] $SlugForm
 * @property int[] $SourceId
 * @property string $SourceIdForm
 * @property Text[] $ShortDesc
 * @property string[] $ShortDescForm
 * @property Text[] $LongDesc
 * @property string[] $LongDescForm
 * */
class Category extends MongoActiveRecord
{
    public $activeLangs = [];
    public $multiLangAttrs = [
        'Title',
        'Slug',
        'ShortDesc',
        'LongDesc'
    ];

    /**
     * @return string the name of the index associated with this ActiveRecord class.
     */
    public static function collectionName()
    {
        return 'VideoCategory';
    }

    public function formName()
    {
        return "c";
    }

    public function scenarios()
    {
        $attr = ['_id', 'Title', 'SourceId', 'SourceIdForm', 'TitleForm','SlugForm','LongDesc','ShortDesc','LongDescForm','ShortDescForm'];
        return [
            'create' => $attr,
            'update' => $attr,
        ];
    }

    /**
     * @return array list of attribute names.
     */
    public function attributes()
    {
        return [
            '_id',
            'Title',
            'SourceId',
            'Slug',
            'LongDesc',
            'ShortDesc',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Title','SourceId','Slug'], 'required'],
            [['TitleForm','SourceIdForm','SlugForm','LongDescForm','ShortDescForm','LongDesc','ShortDesc'],'safe'],
        ];
    }

    /**
     * TODO: убрать куда-нибудь всё это сеттеро-геттеровое дерьмо.
     * Хорошая идея отнаследовать родной метод для получения переменных и в зависимости от переменной MultlangAttributes
     * выдавать необходимую структуру Text[]
     */


    public function getSourceIdForm() {
        if (!empty($this->SourceId)) {
            return implode(",", $this->SourceId);
        }
        return null;
    }

    public function setSourceIdForm($sif) {
        $tmp_sourceId = array_map('intval',explode(',',$sif));
        $this->SourceId = $tmp_sourceId;
    }

    public function getTitleForm() {
        if (empty($this->Title)) {
            return ["ru" => ''];
        } else {
            $titleForm = [];
            foreach ($this->Title as $text) {
                $titleForm[$text['Language']] = $text['Quote'];
            }
            return $titleForm;
        }
    }

    public function getLongDescForm() {
        if (empty($this->LongDesc)) {
            return ["ru" => ''];
        } else {
            $longDescForm = [];
            foreach ($this->LongDesc as $text) {
                $longDescForm[$text['Language']] = $text['Quote'];
            }
            return $longDescForm;
        }
    }

    public function setLongDescForm($input) {
        $longDesc = [];
        foreach ($input as $k => $v) {
            if (empty($v)) {
                continue;
            }
            $longDesc[] = [
                'Language' => Language::getKey(Language::getValue($k)),
                'Quote' => $v,
            ];
        }
        $this->LongDesc = $longDesc;
    }

    public function setShortDescForm($input) {
        $shortDesc = [];
        foreach ($input as $k => $v) {
            if (empty($v)) {
                continue;
            }
            $shortDesc[] = [
                'Language' => Language::getKey(Language::getValue($k)),
                'Quote' => $v,
            ];
        }
        $this->ShortDesc = $shortDesc;
    }

    public function getShortDescForm() {
        if (empty($this->ShortDesc)) {
            return ["ru" => ''];
        } else {
            $shortDescForm = [];
            foreach ($this->ShortDesc as $text) {
                $shortDescForm[$text['Language']] = $text['Quote'];
            }
            return $shortDescForm;
        }
    }

    public function setTitleForm($titleForm) {
        $title = [];
        foreach ($titleForm as $k => $v) {
            if (empty($v)) {
                continue;
            }
            $title[] = [
                'Language' => Language::getKey(Language::getValue($k)),
                'Quote' => $v,
            ];
        }
        $this->Title = $title;
    }

    public function getSlugView() {
        if (!empty($this->Slug)) {
            return $this->Slug;
        }
        return [];
    }
    public function getLongDescView() {
        if (!empty($this->LongDesc)) {
            return $this->LongDesc;
        }
        return [];
    }
    public function getShortDescView() {
        if (!empty($this->ShortDesc)) {
            return $this->ShortDesc;
        }
        return [];
    }

    public function getSlugForm() {
        if (empty($this->Slug)) {
            return ["ru" => ''];
        } else {
            $slugForm = [];
            foreach ($this->Slug as $slug) {
                $slugForm[$slug['Language']] = $slug['Quote'];
            }
            return $slugForm;
        }
    }

    public function setSlugForm($slugForm) {
        $slug = [];
        foreach ($slugForm as $k => $v) {
            if (empty($v)) {
                continue;
            }
            $slug[] = [
                'Language' => Language::getKey(Language::getValue($k)),
                'Quote' => Transliterator::alterate($v,Language::getKey(Language::getValue($k))),
            ];
        }
        $this->Slug = $slug;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('dict', 'Id'),
            'Title' => Yii::t('dict', 'Title'),
            'SourceId' => Yii::t('dict', 'Source'),
            'SourceIdForm' => Yii::t('dict', 'Source'),
            'TitleForm' => Yii::t('dict', 'Title'),
            'SlugForm' => Yii::t('dict', 'Slug'),
            'ShortDesc' => Yii::t('dict', 'Short Description'),
            'LongDesc' => Yii::t('dict', 'Long Description'),
        ];
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
     * pre-Traits methods
     * Очень похожие методы есть во многих частях приложения. Можно подумать о том, чтобы вынести их в трейты
     * ====================================================
     */

    /**
     * Возвращает список активных языков у модели
     * @return array
     */
    public function getActiveLangs()
    {
        if (!empty($this->activeLangs)) {
            return $this->activeLangs;
        }
        $this->activeLangs = array_unique(array_merge(array_keys($this->TitleForm), array_keys(array_filter($this->LongDescForm)), array_keys($this->ShortDescForm)));
        return $this->activeLangs;
    }

    /**
     * Функция для полученя значения мультиязычного аттрибута
     * @param string $attr
     * @param string $lang
     * @return string
     */
    public function getLangAttr($attr='Title',$lang='ru') {
        if (!in_array($attr, $this->multiLangAttrs)) {
            throw new InvalidParamException('Param ' . $attr . ' is not multilangual');
        }

        $attrVal = $this->getAttribute($attr);
        if (is_array($attrVal) && count($attrVal) > 0) {
            foreach ($attrVal as $val) {
                if ($val['Language'] == $lang) {
                    return $val['Quote'];
                }
            }
        }
        return '';
    }
}