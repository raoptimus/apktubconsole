<?php

namespace app\models\video;

use app\components\CustomEvents;
use app\components\MongoActiveRecord;
use app\components\Transliterator;
use app\models\Text;
use MongoDate;
use Yii;
use app\components\behaviors\consoleLogger;

/**
 *
 * @property int $_id
 * @property VideoSource $Source
 * @property File[] $Files
 * @property Screenshot[] $Screenshots
 * @property Comment[] $Comments
 * @property Text[] $Title
 * @property Text[] $Desc
 * @property int $Duration
 * @property int $CategoryId
 * @property MongoDate $PublishedDate
 * @property []int $Related
 * @property int $ViewCount
 * @property int $LikeCount
 * @property int $DownloadCount
 * @property int $CommentCount
 * @property MongoDate $AddedDate
 * @property array $Tags
 * @property array $Filters
 * @property MongoDate $UpdateDate
 * @property array $TitleForm
 * @property array $DescForm
 * @property array $TagsForm
 * @property string[] $Keywords
 * @property string[] $Actors
 * @property int $ChannelId
 * @property float $Rank
 * @property Text[] $Slug
 */
class Video extends MongoActiveRecord
{
    public $tag_proxy;
    public $activeLangs = [];

    public function behaviors()
    {
        return [
            consoleLogger::className()
        ];
    }

    public function getId()
    {
        return (string)$this->_id;
    }

    /**
     * Функция для получения превьюхи
     *
     * @param string $size
     * @return string
     * @throws \Exception
     */
    public function getThumb($size = '180x240')
    {
        $thumbRoot = \Yii::getAlias('@webroot') . Yii::$app->params['thumbCachePath'] . $this->_id . '/';
        $thumbDirectory = $thumbRoot . $size . '/';
        $thumbFile = $thumbDirectory . 'thumb.jpg';

        if (file_exists($thumbFile)) {
            return Yii::$app->params['thumbCachePath'] . "{$this->_id}/{$size}/thumb.jpg";
        } else {
            if (!is_dir($thumbDirectory)) {
                mkdir($thumbDirectory, 0777, true);
            }

            //для начала проверим, может быть мы уже скачивали оригинал
            $path_to_origin = $thumbRoot . '640x480/thumb.jpg';

            if (!file_exists($path_to_origin)) {
                //на всякий пожарный проверим, есть ли у нас директория
                if (!is_dir($thumbRoot . '640x480')) {
                    mkdir($thumbRoot . '640x480', 0777, true);
                }

                $remote_url = $this->getOriginThumbSrc($this->Source['ScreenshotSelectIndex']);
                file_put_contents($path_to_origin, file_get_contents($remote_url));
            }

            return $this->getCroppedThumb($path_to_origin, $size, $thumbDirectory);
        }
    }

    /**
     * Функция для получения ссылки на исходную превьюху
     *
     * @param $index
     * @return string
     */
    public function getOriginThumbSrc($index)
    {
        return sprintf(Yii::$app->params["videoThumbCdnUrl"] . "/%s/%s/%d/%dx%d/%010d.jpg",
            substr($this->Source['SourceId'], 0, 2),
            substr($this->Source['SourceId'], 2, 2),
            $this->Source['SourceId'], 640, 480, $index);
    }

    /**
     * Функция для кропа превьюхи под определённый формат
     *
     * @param $path
     * @param $thumbDirectory
     * @param $size
     * @return mixed
     * @throws \Exception
     */
    public function getCroppedThumb($path, $size, $thumbDirectory)
    {
        $size = strtolower($size);
        $sizeArray = explode('x', $size);
        if (count($sizeArray) < 2) {
            throw new \Exception('Wrong Size Format!');
        }
        $width = $sizeArray[0];
        $height = $sizeArray[1];

        $im = new \Imagick($path);
        $im->cropThumbnailImage($width, $height);
        $im->writeImage($thumbDirectory . 'thumb.jpg');

        return Yii::$app->params['thumbCachePath'] . "$this->_id/$size/thumb.jpg";
    }

    public function posterUrl()
    {
        return '/' . Yii::$app->params['project'] . "/video/get-thumb?id={$this->_id}&size=640x480";
    }

    public function getPlayerOptions()
    {
        //базовые опции
        $options = [
            'path' => "http://cdn.12player..../last",
            'containerId' => "video_container_$this->_id",
            'hideControls' => false,
            'autoStart' => false,
            'defaultPlayer' => "html5",
            'preload' => false,
            'streamingParam' => "start",
            'playlist' => [
                0 => [
                    "introImage" => '/' . Yii::$app->params['project'] . "/video/get-thumb?id=$this->_id&size=640x480"
                ]
            ]
        ];

        //а теперь надо получить файлы
        foreach ($this->Files as $file) {
            $options['playlist'][0]['videos'][$file['H']] = [
                'fileUrl' => $this->getFileUrl($file),
                'isDefault' => $file['H'] == 480
            ];
        }

        return $options;
    }

    public function getFileUrl(array $file)
    {
        $ttl = 7200;
        $creation =  (int)gmdate("U");
        $expires = $creation + $ttl;
        $uri = $file['Path'] . $file['Name'];
        $ip = $_SERVER['REMOTE_ADDR'];
        $key = Yii::$app->params["videoCdnKey"];
        $baseUrl = ltrim(Yii::$app->params["videoCdnUrl"], "/");

        switch (Yii::$app->params['videoAntihotMod']) {
            case 'modsec': {
                $hash = strtr(base64_encode(md5($key . $uri . $expires . $ip, true)),
                    ['+' => '-', '/' => '_', '=' => '']);

                return $baseUrl . "/" . $hash . "/" . $expires . $uri . "?ip=" . $ip;
            }
            case 'ucdn': {
                $hash = md5($uri . $key . $creation . $ttl);
                return $baseUrl . $uri . '?cdn_hash=' . $hash . '&cdn_creation_time=' . $creation . '&cdn_ttl=' . $ttl;
            }
        }

        return $baseUrl . $uri;
    }

    public function refreshIndexThumb()
    {
        $thumbRoot = \Yii::getAlias('@webroot') . Yii::$app->params['thumbCachePath'] . $this->_id . '/';
        system("rm -rf " . $thumbRoot);
        $this->getThumb('300x180');
    }

    public function getAllThumbs()
    {
        $returnArray = [];
        for ($index = 1; $index <= $this->Source['ScreenshotCount']; $index++) {
            $returnArray[] = [
                'index' => $index,
                'src' => $this->getOriginThumbSrc($index),
                'selected' => $index == $this->Source['ScreenshotSelectIndex'] ? true : false
            ];
        }
        return $returnArray;
    }

    /**
     * @param int $index
     * @return bool
     */
    public function saveIndexThumb($index)
    {
        if (!$index) {
            return false;
        }
        $Source = $this->Source;
        $Source['ScreenshotSelectIndex'] = intval($index);
        $this->Source = $Source;

        if ($this->update(false, ['Source'])) {
            $this->refreshIndexThumb();
            return true;
        }

        return false;
    }

    public function up()
    {
        $this->PublishedDate = new \MongoDate();
        return $this->update(false, ['PublishedDate']);
    }

    public function featured()
    {
        return in_array("featured", $this->Filters);
    }

    public function deleted()
    {
        return in_array("deleted", $this->Filters);
    }

    public function toggleFeatured()
    {
        $this->Filters = array_map(function ($v) {
            switch ($v) {
                case "featured":
                    return "!featured";
                case "!featured":
                    return "featured";
                default:
                    return $v;
            }
        }, $this->Filters);

        return $this->update(false, ['Filters']);
    }

    public static function getThumbByIdAndSize($id, $size)
    {
        $video = Video::findOne(intval($id));
        return \Yii::getAlias('@webroot') . $video->getThumb($size);
    }

    public function getLangTitle($lang)
    {
        if (empty($lang)) {
            $lang = 'ru';
        }
        foreach ($this->Title as $title) {
            if ($title['Language'] == $lang) {
                return $title['Quote'];
            }
        }
        return "";
    }

    public function getLangDesc($lang)
    {
        if (empty($lang)) {
            $lang = 'ru';
        }
        foreach ($this->Desc as $desc) {
            if ($desc['Language'] == $lang) {
                return $desc['Quote'];
            }
        }
        return "";
    }

    public function getTitleForm()
    {
        $a = [];
        foreach ($this->Title as $title) {
            $a[$title['Language']] = $title['Quote'];
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

    public function getDescForm()
    {
        $a = [];
        foreach ($this->Desc as $desc) {
            /**
             * @TODO: прочитать, как делаются фильтры и перенести туда
             */
            if (empty($desc)) {
                continue;
            }
            $a[$desc['Language']] = $desc['Quote'];
        }
        return $a;
    }

    public function setDescForm($value)
    {
        $a = [];
        foreach ($value as $lang => $desc) {
            $a[] = [
                'Quote' => $desc,
                'Language' => $lang
            ];
        }
        $this->Desc = $a;
    }

    public function getTagsForm()
    {
        $tmp_array = [];
        foreach ($this->Tags as $tag) {
            $tmp_array[$tag['Language']] = implode(', ', array_unique(array_filter($tag['Tags'])));
        }
        return $tmp_array;
    }

    public function setTagsForm($value)
    {
        $value = array_filter($value);
        $tagValue = [];
        foreach ($value as $lang => $tagsString) {
            $tagValue[] = [
                'Language' => $lang,
                'Tags' => $this->filterTags($tagsString)
            ];
        }
        $this->Tags = $tagValue;
    }

    public function spamComment($commentId)
    {
        $theComment = [];
        foreach ($this->Comments as $comment) {
            if ($comment['_id'] == $commentId) {
                $theComment = $comment;
                break;
            }
        };
        $theComment['Status'] = 1;

        $this->getCollection()->update(['_id' => intval($this->_id)], ['$pull' => ['Comments' => ['_id' => $commentId]]]);
        return $this->getCollection()->update(['_id' => intval($this->_id)], ['$push' => ['Comments' => $theComment]]);
    }

    public function deleteComment($commentId)
    {
        return $this->getCollection()->update(['_id' => intval($this->_id)], ['$pull' => ['Comments' => ['_id' => $commentId]]]);
    }

    public function getActiveLangs()
    {
        if (!empty($this->activeLangs)) {
            return $this->activeLangs;
        }
        $this->activeLangs = array_unique(array_merge(array_keys($this->TitleForm), array_keys(array_filter($this->DescForm)), array_keys($this->TagsForm)));
        return $this->activeLangs;
    }

    public function getActorsForm()
    {
        if (empty($this->Actors)) {
            return '';
        }

        return implode(', ', $this->Actors);
    }

    public function setActorsForm($input)
    {
        $this->Actors = array_map('trim', explode(',', $input));
    }

    public function getKeywordsForm()
    {
        if (empty($this->Keywords)) {
            return '';
        }
        return implode(', ', $this->Keywords);
    }

    public function setKeywordsForm($input)
    {
        $this->Keywords = array_map('trim', explode(',', $input));
    }

    public function getPublishedDateForm()
    {
        if (empty($this->PublishedDate) || in_array('!approved', $this->Filters)) {
            $dt = new \DateTime('tomorrow');
            return $dt->format('d-m-Y H:i');
        }
        return date('d-m-Y H:i', $this->PublishedDate->sec);
    }

    public function setPublishedDateForm($input)
    {
        $this->PublishedDate = new MongoDate(date_create_from_format('d-m-Y H:i', $input)->getTimestamp());
    }

    /**
     * Override methods
     * ====================================================
     */

    public function init()
    {
        //set default values
        $this->UpdateDate = new MongoDate();
    }

    public function afterFind()
    {
        if (empty($this->Filters)) {
            $this->Filters = ["featured"];
        }
    }


    /**
     * Данная функция удаляет все теги, использующиеся в процессе публикации и оставляет толлько deleted
     * @return bool|int
     */
    public function delete()
    {
        $filters = $this->Filters;

        $filters = array_filter($filters, function($el) {
            if (in_array($el, ['approved','!approved', 'published', 'deleted'])) {
                return false;
            }
            return true;
        });

        $filters[] = "deleted";

        $this->Filters = array_values($filters);
        return $this->update(false, ['Filters']);
    }

    /**
     * Данная функция убирает все элементы 'approved' и '!approved' из фильтров
     * И устанавливает только один 'approved', подготавливая элемент к публикации.
     * @version 2 в первой версии мы оперировали методами $pull и $push монги, что приводило к ошибкам.
     * @return bool|int
     */
    public function approve()
    {
        $fields = [];

        //для начала очищаем фильтры
        $filters = $this->Filters;
        $filters = array_filter($filters, function($el) {
            if ($el == 'approved' || $el == '!approved') {
                return false;
            }
            return true;
        });

        //добавляем значение
        $filters[] = 'approved';
        $this->Filters = array_values($filters);
        $fields[] = 'Filters';

        //а так же обновляем слаги.
        $this->refreshSlug();
        $fields[] = 'Slug';

        //и обновляем дату публикации.
        if (!($this->PublishedDate instanceof MongoDate) || $this->PublishedDate->sec < time()) {
            $this->PublishedDate = new \MongoDate();
            $fields[] = 'PublishedDate';
        }

        return $this->update(false, $fields);
    }

    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'Video';
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',

            'Source',
            'Source.SourceId',

            'Files',
            'Screenshots',
            'Comments',

            'Title',
            'Slug',

            'Desc',
            'Duration',
            'CategoryId',
            'PublishedDate',
            'Related',
            'ViewCount',
            'LikeCount',
            'DownloadCount',
            'CommentCount',
            'AddedDate',
            'Tags',
            'Filters',
            'UpdateDate',
            'ChannelId',
            'Keywords',
            'Actors',
            'Rank',
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
    public function rules()
    {
        return [
            [['_id', 'Duration', 'CategoryId', 'PublishedDate', 'UpdateDate', 'Title', 'Desc'], 'required'],
            [['DescForm', 'TitleForm', 'TagsForm', 'ActorsForm', 'KeywordsForm', 'PublishedDateForm', 'Actors', 'Slug'], 'safe'],
            [['ChannelId'], 'integer'],
            [['Rank'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('dict', 'Id'),
            'Source' => Yii::t('dict', 'Source'),
            'Files' => Yii::t('dict', 'Files'),
            'Screenshots' => Yii::t('dict', 'Screenshots'),
            'Comments' => Yii::t('dict', 'Comments'),
            'Title' => Yii::t('dict', 'Title'),
            'Desc' => Yii::t('dict', 'Desc'),
            'Duration' => Yii::t('dict', 'Duration'),
            'CategoryId' => Yii::t('dict', 'CategoryId'),
            'PublishedDate' => Yii::t('dict', 'PublishedDate'),
            'Related' => Yii::t('dict', 'Related'),
            'ViewCount' => Yii::t('dict', 'ViewCount'),
            'LikeCount' => Yii::t('dict', 'LikeCount'),
            'DownloadCount' => Yii::t('dict', 'DownloadCount'),
            'CommentCount' => Yii::t('dict', 'CommentCount'),
            'AddedDate' => Yii::t('dict', 'AddedDate'),
            'Tags' => Yii::t('dict', 'Tags'),
            'Filters' => Yii::t('dict', 'Filters'),
            'TitleForm' => Yii::t('dict', 'Title'),
            'DescForm' => Yii::t('dict', 'Description'),
            'TagsForm' => Yii::t('dict', 'Tags'),
            'UpdateDate' => Yii::t('dict', 'Update date'),
            'ChannelId' => Yii::t('dict', 'Channel'),
            'Actors' => Yii::t('dict', 'Actors'),
            'ActorsForm' => Yii::t('dict', 'Actors'),
            'KeywordsForm' => Yii::t('dict', 'Keywords'),
            'PublishedDateForm' => Yii::t('dict', 'Published date'),
            'Rank' => Yii::t('dict', 'Rank'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        $this->UpdateDate = new MongoDate();
        $this->CategoryId = intval($this->CategoryId);

        //заполняем keywords
        //сначала теги
        $tmp_keywords = $this->Keywords ?: [];
        foreach ($this->Tags as $langTags) {
            $tmp_keywords = array_merge($tmp_keywords, $langTags['Tags']);
        }

        //добавляем категорию
        $cat = Category::findOne(['_id' => intval($this->CategoryId)]);
        foreach ($cat->Title as $catTitle) {
            $tmp_keywords[] = $catTitle['Quote'];
        }

        //добавляем канал
        $channel = Channel::findOne(['_id' => intval($this->ChannelId)]);
        if (is_object($channel)) {
            $tmp_keywords[] = $channel->Title;
        }

        //добавляем актёров
        $tmp_keywords = array_merge($tmp_keywords, $this->Actors ?: []);

        //проверяем на уникальность
        $tmp_keywords = array_filter(array_unique(array_map(function ($el) {
            return mb_strtolower($el, 'UTF-8');
        }, $tmp_keywords)));

        //отбросим стоп-слова, если надо
        if (isset(Yii::$app->params["excludeKeywords"])) {
            $tmp_keywords = array_diff(
                $tmp_keywords,
                array_map(function ($el) {
                    return mb_strtolower($el, 'UTF-8');
                }, Yii::$app->params["excludeKeywords"])
            );
        }

        $this->Keywords = array_values($tmp_keywords);

        /*        //сначала надо удалить все исчезнувшие переводы
                foreach ($this->oldAttributes['Tags'] as $oldTags) {

                }

                echo('<pre>');
                print_r($this->Tags);
                print_r($this->oldAttributes['Tags']);
                exit;

                //надо заполнить переводы тегов
                $rawTags = [];
                foreach ($this->Tags as $tagList) {
                    $tags = Tag::find()
                        ->where([
                            'Title.Quote' => $tagList['Tags'],
                            'Title.Language' => $tagList['Language'],
                        ])->all();

                    foreach ($tags as $tag) {
                        foreach ($tag->Title as $title) {
                            $rawTags[$title['Language']][] = $title['Quote'];
                        }
                    }
                }
                $cleanTags = [];
                foreach ($rawTags as $lang => $tags) {
                    $cleanTags[] = [
                        'Language' => $lang,
                        'Tags' => array_values(array_unique($tags))
                    ];
                }
                $this->Tags = $cleanTags;*/
        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        //если новая запись - зажигаем евент создания видео
        if ($insert) {
            $this->trigger(CustomEvents::EVENT_VIDEO_CREATED);
        } else {
            $this->trigger(CustomEvents::EVENT_VIDEO_UPDATED);
        }

        if (isset($changedAttributes['Tags'])) {
            //вычисляем разницы в тегах
            //u = useful
            $uChangedTags = Tag::convertToUseful($changedAttributes['Tags']);
            $uOldTags = Tag::convertToUseful($this->oldAttributes['Tags']);
            $langs = array_unique(array_keys($uChangedTags) + array_keys($uOldTags));

            $tagsToAdd = [];
            $tagsToRemove = [];

            foreach ($langs as $lang) {
                $tagsToAdd[$lang] = array_diff(
                    isset($uOldTags[$lang]) ? $uOldTags[$lang] : [],
                    isset($uChangedTags[$lang]) ? $uChangedTags[$lang] : []
                );
                $tagsToRemove[$lang] = array_diff(
                    isset($uChangedTags[$lang]) ? $uChangedTags[$lang] : [],
                    isset($uOldTags[$lang]) ? $uOldTags[$lang] : []
                );
            }

            //вычислили. Молодцы
            $tagsToRemove = array_filter($tagsToRemove);
            $tagsToAdd = array_filter($tagsToAdd);

            //декриментим VideoCount у тегов, которые исчезли из видео
            foreach ($tagsToRemove as $lang => $tags) {
                foreach ($tags as $strTag) {
                    $tmp_tag = Tag::find()
                        ->where([
                            'Title.Language' => $lang,
                            'Title.Quote' => $strTag
                        ])->one();
                    if ($tmp_tag) {
                        /**
                         * @var Tag $tmp_tag
                         */
                        $tmp_tag->decVideoCount();
                    }
                }
            }

            //инкриментим VideoCount у тегов, которые добавились в видео, либо добавляем новые
            foreach ($tagsToAdd as $lang => $tags) {
                foreach ($tags as $strTag) {
                    $tmp_tag = Tag::find()
                        ->where([
                            'Title.Language' => $lang,
                            'Title.Quote' => $strTag
                        ])->one();
                    if ($tmp_tag) {
                        /**
                         * @var Tag $tmp_tag
                         */
                        $tmp_tag->incVideoCount();
                    } else {
                        $tmp_tag = new Tag();
                        $tmp_tag->VideoCount = 1;
                        $tmp_tag->Title = [
                            [
                                'Language' => $lang,
                                'Quote' => $strTag
                            ]
                        ];
                        $tmp_tag->save();
                    }
                }
            }
        }

        //после долгих споров с самим собой и татриксом было решено,
        //что де-факто так делать не правильно и в данном случае можно было обойтись пересохранением массива.
        //но де-юро надо, конечно, поступать так из-за возможности внешних изменений модели
        //так что фигачим через $pull/$push, как бы мне это не нравилось в данном конкретном случае.

        //чистим категории
        if (isset($changedAttributes['CategoryId'])) {
            $this->getCollection()->update(['_id' => intval($this->_id)], ['$pull' => ['Filters' => 'c' . $changedAttributes['CategoryId']]]);
            $this->getCollection()->update(['_id' => intval($this->_id)], ['$push' => ['Filters' => 'c' . $this->CategoryId]]);
        }

        //чистим каналы
        if (isset($changedAttributes['ChannelId'])) {
            $this->getCollection()->update(['_id' => intval($this->_id)], ['$pull' => ['Filters' => 'ch' . $changedAttributes['ChannelId']]]);
            $this->getCollection()->update(['_id' => intval($this->_id)], ['$push' => ['Filters' => 'ch' . $this->ChannelId]]);
        }

        //актёры.
        //вычищаем актёров из фильтров
        foreach ($this->Filters as $filter) {
            if (preg_match('/[a]\d+/', $filter)) {
                $this->getCollection()->update(['_id' => intval($this->_id)], ['$pull' => ['Filters' => $filter]]);
            }
        }
        //Забираем справочник актёров
        $actorRawDict = Actor::find()->where(['Name' => $this->Actors])->all();
        $actorDict = [];
        foreach ($actorRawDict as $dictElement) {
            $actorDict[$dictElement->_id] = $dictElement->Name;
        }

        //Создаём несозданных актёров и добавляем их в Фильтры
        if (count($this->Actors)) {
            foreach ($this->Actors as $actorName) {
                if (!in_array($actorName, $actorDict)) {
                    $a = new Actor();
                    $a->Name = $actorName;
                    $a->save();
                    $actorDict[$a->_id] = $a->Name;
                }
                $this->getCollection()->update(['_id' => intval($this->_id)], ['$push' => ['Filters' => 'a' . array_search($actorName, $actorDict)]]);
            }
        }

        return parent::afterSave($insert, $changedAttributes);
    }

    public function update($runValidation = true, $attributeNames = null)
    {
        if (!is_null($attributeNames) && !in_array("UpdateDate", $attributeNames)) {
            $attributeNames[] = "UpdateDate";
        }
        return parent::update($runValidation, $attributeNames);
    }

    /**
     * Private methods
     * ====================================================
     */

    /**
     * @param string $tags
     * @return array
     */
    private function filterTags($tags)
    {
        $res = [];
        $tags = explode(',', $tags);
        foreach ($tags as $tag) {
            $tag = mb_strtolower(trim($tag), "UTF-8");
            if (empty($tag) || in_array($tag, $res)) {
                continue;
            }
            $res[] = $tag;
        }
        return $res;
    }

    /**
     * Устанавливает слаг в соответствии с текущим тайтлом
     * ТОВАРИЩ!! Используя эту функцию - думай, что ты делаешь, так как она не имеет в себе никаких проверок
     */
    private function refreshSlug() {
        $tmpSlug = [];
        foreach ($this->Title as $text) {
            $tmpSlug[] = [
                'Language' => $text['Language'],
                'Quote' =>Transliterator::alterate($text['Quote'], $text['Language'])
            ];
        }
        $this->Slug = $tmpSlug;
    }
}
