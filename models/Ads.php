<?php
/**
 * Created by PhpStorm.
 * User: sainomori
 */

namespace app\models;

use app\components\MongoActiveRecord;
use app\models\files\AdsIcon;
use Yii;
use yii\base\ErrorException;
use yii\web\UploadedFile;
use app\models\files\AdsScreenShot;
use app\models\users\Device;

/**
 * This is the model class for table "Ads".
 * @property \MongoId _id
 * @property array Title
 * @property array Name
 * @property string Icon
 * @property array Desc
 * @property string Age
 * @property string Rating
 * @property string Status
 * @property array Screenshots
 * @property array TitleForm
 * @property array NameForm
 * @property array DescForm
 * @property mixed id
 * @property integer Sort
 * @property array Countries
 * @property array CarrierType
 */
class Ads extends MongoActiveRecord
{
    public $IconForm;
    public $ScreenShotsForm;
    public $activeLangs = [];

    public static function getDb()
    {
        return \Yii::$app->get('mongodb2');
    }

    /**
     * @return string the name of the index associated with this ActiveRecord class.
     */
    public static function collectionName()
    {
        return 'Ads';
    }

    /**
     * @return array list of attribute names.
     */
    public function attributes()
    {
        return [
            '_id',
            'Title',
            'Name',
            'Icon',
            'Desc',
            'Age',
            'Rating',
            'Screenshots',
            'Status',
            'Link',
            'Sort',
            'Countries',
            'CarrierType',
            'Note'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Icon', 'Link','Note'], 'string'],
            [['Sort'], 'integer'],
            [['Link'], 'url'],
            [
                'Status',
                'in',
                'range' => ['Running', 'Stopped', 'Deleted'],
            ],
            [['Title', 'Name', 'Desc', 'Countries', 'CarrierType'], 'required'],
            [['_id', 'Screenshots', 'TitleForm', 'NameForm', 'DescForm', 'Age', 'Rating', 'CountriesForm', 'CarrierType'], 'safe'],
            ['Screenshots', 'validateScreenShots', 'skipOnEmpty' => false],
            [['ScreenShotsForm'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg', 'maxFiles' => 4],
        ];
    }

    /**
     * Валидатор для Скриншотов
     * @param $attribute
     * @param $params
     * @return bool
     */
    public function validateScreenShots($attribute, $params)
    {
        //при отсутствии скриншотов нельзя включать объявление
        if (empty($params) && (count($this->$attribute) == 0)) {
            if ($this->Status == 'Running') {
                $this->addError('Status', 'Нельзя включать объявление, у которого нет скриншотов');
                return false;
            }
        }
        return true;
    }

    public static function switchOrder($newOrder = []) {
        if (empty($newOrder)) {
            return false;
        }

        $oldOrders = [];
        //запоминаем старые сортировки
        foreach ($newOrder as $el) {
            $model = self::findOne(['_id' => intval($el)]);
            $oldOrders[$el] = $model->Sort;
        }

        //надо проверить на дубликаты
        //но при этом порядок нам, в общем, не интересен - тут только указатели на старые данные
        if (count($oldOrders) != count(array_unique($oldOrders))) {
            asort($oldOrders);
            $prevValue = min($oldOrders) - 1;
            foreach ($oldOrders as &$el) {
                if ($el <= $prevValue) {
                    $el = $prevValue + 1;
                }
                $prevValue = $el;
            }
            unset($el);
        }

        foreach ($newOrder as $key => $value) {
            $model = self::findOne(['_id' => intval($key)]);
            $model->Sort = $oldOrders[$value];
            $model->update(false,['Sort']);
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('dict', 'Id'),
            'Title' => Yii::t('dict', 'Ads Title'),
            'Name' => Yii::t('dict', 'Ads Name'),
            'TitleForm' => Yii::t('dict', 'Ads Title'),
            'NameForm' => Yii::t('dict', 'Ads Name'),
            'Icon' => Yii::t('dict', 'Icon'),
            'IconForm' => Yii::t('dict', 'Icon'),
            'Desc' => Yii::t('dict', 'Desc'),
            'DescForm' => Yii::t('dict', 'Desc'),
            'Age' => Yii::t('dict', 'Age'),
            'Rating' => Yii::t('dict', 'Rating'),
            'Screenshots' => Yii::t('dict', 'Screenshots'),
            'ScreenShotsForm' => Yii::t('dict', 'Screenshots'),
            'Link' => Yii::t('dict', 'Link'),
            'Sort' => Yii::t('dict', 'Sort'),
            'Status' => Yii::t('dict', 'Status'),
            'CountriesForm' => Yii::t('dict', 'Countries'),
            'CarrierType' => Yii::t('dict', 'CarrierType'),
        ];
    }

    public function getId()
    {
        return (string)$this->_id;
    }

    public function setIconForm(UploadedFile $icon)
    {
        if (!in_array($icon->type, ['image/jpeg', 'image/png'])) {
            $this->addError('Icon', 'Icon should be JPEG or PNG');
        }

        $im = new \Imagick($icon->tempName);
        $im->cropThumbnailImage(240, 240);
        $im->writeImage($icon->tempName);
        $iconObj = new AdsIcon();
        $iconObj->file = $icon;
        $iconObj->size = filesize($icon->tempName);
        $iconObj->contentType = $icon->type;
        if ($iconObj->save()) {
            $this->Icon = $iconObj->id;
        } else {
            $this->addError('Icon', 'There were a problem while saving Icon');
        }
    }

    public function setScreenShotsForm(array $files)
    {
        if (count($files)) {
            $tmpScreenShotHolder = $this->Screenshots;
            foreach ($files as $screenShot) {
                //совершенно неожиданно, что нет функции, вписывающей изображение в прямоугольник о.0
                //товарищ, если ты знаешь про существавание оной - отбрось этот костыль.
                $imagick = new \Imagick($screenShot->tempName);
                $imageHeight = $imagick->getImageHeight();
                $imageWidth = $imagick->getImageWidth();
                //определяем новый размер
                if ($imageHeight > 636 || $imageWidth > 1272) {
                    if ($imageWidth / 1272 > $imageHeight / 636) {
                        $newWidth = 1272;
                        $newHeight = round($imageHeight * 1272 / $imageWidth);
                    } else {
                        $newWidth = round($imageWidth * 636 / $imageHeight);
                        $newHeight = 636;
                    }
                    $imagick->scaleImage($newWidth, $newHeight);
                    $imagick->writeImage($screenShot->tempName);
                }

                $screenShot->size = filesize($screenShot->tempName);
                $shot = new AdsScreenShot();
                $shot->file = $screenShot;
                $shot->size = $screenShot->size;
                $shot->contentType = $screenShot->type;
                $shot->save();
                $tmpScreenShotHolder[] = $shot->id;
            }
            $this->Screenshots = array_filter($tmpScreenShotHolder);
        }
    }

    /**
     * Функция, удаляющая иконку или скриншот из модели
     * Для удаления иконки функция должна быть вызвана с пустым значением shot_id
     * Если же shot_id передан - удаляется скриншот с данным id
     * @param $shot_id
     * @return bool
     * @throws ErrorException
     */
    public function deleteIcon($shot_id = '')
    {
        if (empty($shot_id)) {
            //удаляем иконку
            if (empty($this->Icon)) {
                return true;
            }

            $iconModel = AdsIcon::findOne(['_id' => $this->Icon]);

            $this->Icon = "";
            if ($this->save()) {
                if ($iconModel->delete()) {
                    return true;
                } else {
                    throw new ErrorException('Model was saved, but icon file was not deleted');
                }
            } else {
                throw new ErrorException('Error while saving model');
            }
        } else {
            //удаляем скриншот с данным id
            if (empty($this->Screenshots)) {
                return true;
            }

            $tmp_shots = [];
            $delete_shot = '';
            foreach ($this->Screenshots as $shot) {
                if ($shot == $shot_id) {
                    $delete_shot = $shot;
                } else {
                    $tmp_shots[] = $shot;
                }
            }
            $this->Screenshots = $tmp_shots;

            if ($this->save()) {
                $shotModel = AdsScreenShot::findOne(['_id' => $delete_shot]);
                if ($shotModel->delete()) {
                    return true;
                } else {
                    throw new ErrorException('Model was saved, but screenshot file was not deleted');
                }
            } else {
                throw new ErrorException('Can\'t save model for some reason');
            }
            //не работает =(
            //return $this->getCollection()->update(['_id' => $this->id], ['$pull' => ['Screenshots' => $icon_id]]);
        }
    }

    /**
     * @param array $order — массив id скриншотов, в желаемом порядке
     */
    public function sortShots($order = [])
    {
        // проверяем что нет лишних айди
        $newShots = array_intersect($order, $this->Screenshots);
        // добавляем недостающие
        $newShots = array_merge($newShots, array_diff($this->Screenshots, $newShots));
        $this->Screenshots = array_filter(array_unique($newShots));

        return $this->update(true,['Screenshots']);
    }

    public function getTitleForm()
    {
        $a = [];
        if (!empty($this->Title)) {
            foreach ($this->Title as $title) {
                $a[$title['Language']] = $title['Quote'];
            }
        } else {
            $a['ru'] = Language::getValue('ru');
        }
        return $a;
    }

    public function setTitleForm($value)
    {
        $a = [];
        foreach ($value as $lang => $title) {
            /**
             * @TODO: прочитать, как делаются фильтры и перенести туда
             */
            if (empty($title)) {
                continue;
            }

            $a[] = [
                'Quote' => $title,
                'Language' => $lang
            ];
        }
        $this->Title = $a;
    }

    public function getNameForm()
    {
        $a = [];
        if (!empty($this->Name)) {
            foreach ($this->Name as $name) {
                $a[$name['Language']] = $name['Quote'];
            }
        }
        return $a;
    }

    public function setNameForm($value)
    {
        $a = [];
        foreach ($value as $lang => $name) {
            /**
             * @TODO: прочитать, как делаются фильтры и перенести туда
             */
            if (empty($name)) {
                continue;
            }

            $a[] = [
                'Quote' => $name,
                'Language' => $lang
            ];
        }
        $this->Name = $a;
    }

    public function getDescForm()
    {
        $a = [];
        if (!empty($this->Desc)) {
            foreach ($this->Desc as $desc) {
                $a[$desc['Language']] = $desc['Quote'];
            }
        }
        return $a;
    }

    public function getMaxSort() {
        $result = static::getCollection()->aggregate([
            ['$group' => [
                '_id' => '',
                'max' => [
                    '$max' => '$Sort'
                ]
            ]],
        ]);
        return $result[0]['max'] + 1;
    }

    public function setDescForm($value)
    {
        $a = [];
        foreach ($value as $lang => $desc) {
            /**
             * @TODO: прочитать, как делаются фильтры и перенести туда
             */
            if (empty($desc)) {
                continue;
            }

            $a[] = [
                'Quote' => $desc,
                'Language' => $lang
            ];
        }
        $this->Desc = $a;
    }

    public function getLangAttribute($attribure = 'Title', $lang = 'ru')
    {
        if (!empty($this->{$attribure})) {
            foreach ($this->{$attribure} as $title) {
                if ($title['Language'] == $lang) {
                    return $title['Quote'];
                }
            }
        }
        return "";
    }

    public function getLangTitle($lang)
    {
        if (empty($lang)) {
            $lang = 'ru';
        }
        if (!empty($this->Title)) {
            foreach ($this->Title as $title) {
                if ($title['Language'] == $lang) {
                    return $title['Quote'];
                }
            }
        }
        return "";
    }

    public function getActiveLangs()
    {
        if (!empty($this->activeLangs)) {
            return $this->activeLangs;
        }
        $this->activeLangs = array_unique(array_merge(array_keys($this->TitleForm),
            array_keys(array_filter($this->DescForm)), array_keys($this->NameForm)));
        return $this->activeLangs;
    }

    public function getCountriesForm()
    {
        return array_fill_keys($this->Countries ?: [], 1);
    }

    public function setCountriesForm($value)
    {
        $this->Countries = array_keys(array_filter($value));
    }

    public function init()
    {
        $this->_id = 0;
        $this->Sort = $this->getMaxSort();
    }

    public static function getCarrierTypes()
    {
        return ['wifi' => 'Wi-Fi', 'mobile' => 'мобильный интернет'];
    }

    public function beforeSave($insert)
    {
        $this->Sort = intval($this->Sort);
        $this->Rating = floatval($this->Rating);
        $this->Age = intval($this->Age);
        if (!empty($this->Screenshots)) {
            $this->Screenshots = array_values($this->Screenshots);
        }
        if (!empty($this->Countries)) {
            $this->Countries = array_values($this->Countries);
        }
        return parent::beforeSave($insert);
    }
}