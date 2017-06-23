<?php
/**
 * Created by IntelliJ IDEA.
 * User: ra
 * Date: 09.05.15
 * Time: 15:25
 */

namespace app\models\video;

use app\models\Language;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Class VideoListFilter
 *
 */
class VideoFilter extends Model
{
    public $Search;
    public $Language = "ru";
    public $SortBy = "PublishedDate";
    public $SortDirect = SORT_DESC;
    public $Status = "*";
    public $Category = "*";
    public $isPremium = "*";
    public $score;

    public function formName()
    {
        return "f";
    }

    /**
     * @return array list of attribute names.
     */
    public function attributes()
    {
        return [
            'Search',
            'SortBy',
            'SortDirect',
            'Language',
            'Status',
            'Category',
            'isPremium',
            'score',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Search' => Yii::t('dict', 'Search'),
            'SortBy' => Yii::t('dict', 'SortBy'),
            'SortDirect' => Yii::t('dict', 'SortDirect'),
            'Language' => Yii::t('dict', 'Language'),
            'Status' => Yii::t('dict', 'Status'),
            'Category' => Yii::t('dict', 'Category'),
            'isPremium' => Yii::t('dict', 'isPremium'),
        ];
    }

    public function rules()
    {
        return [
            [
                ['Search', 'Category', 'isPremium', 'score'],
                'string',
                'on' => 'search',
            ],
            [
                'SortBy',
                'in',
                'range' => ["_id", "PublishedDate", "Source.SourceId", "UpdateDate", "Rank"],
                'message' => Yii::t('dict', 'Field is invalid.'),
                'on' => 'search',
            ],
            [
                'SortDirect',
                'in',
                'range' => [SORT_ASC, SORT_DESC],
                'message' => Yii::t('dict', 'Field is invalid.'),
                'on' => 'search',
            ],
            [
                'Status',
                'in',
                'range' => ['*', 'approved', '!approved', 'published', 'deleted'],
                'message' => Yii::t('dict', 'Field is invalid.'),
                'on' => 'search',
            ],
            [
                'Language',
                'in',
                'range' => Language::getKeys(),
                'message' => Yii::t('dict', 'Field is invalid.'),
                'on' => 'search',
            ]
        ];
    }

    private function isSearchText()
    {
        return (!empty($this->Search) && !is_numeric($this->Search));
    }

    /**
     * @param bool|false $listData
     * @return \yii\mongodb\ActiveQuery|\yii\mongodb\Query|static
     */
    public function getQuery($listData = false)
    {
        if (is_numeric($this->Search)) {
            $id = intval($this->Search);
            if ($id == $this->Search) {
                return Video::find()->orWhere(["_id" => $id])->orWhere(["Source.SourceId" => $id]);
            }
        }
        $q = empty($this->Search)
            ? Video::find()
            : Video::findText($this->Search);

        if ($this->Status == '*') {
            $q->andWhere(["Filters" => ['!approved', 'approved', 'published']]);
        } else {
            $q->andWhere(["Filters" => $this->Status]);
        }

        if ($this->Category != '*') {
            $q->andWhere(["Filters" => $this->Category]);
        }
        if ($this->isPremium != '*') {
            $q->andWhere(["Filters" => $this->isPremium]);
        }

        if ($listData && !$this->isSearchText()) {
            $q->select([
                '_id',
                'Title',
                'Desc',
                'Files',
                'PublishedDate',
                'ViewCount',
                'LikeCount',
                'DownloadCount',
                'CommentCount',
                'Screenshots',
                'Source',
                'Filters',
            ]);
        }

        return $q;
    }

    /**
     * @param bool|false $listData
     * @return ActiveDataProvider
     */
    public function getDataProvider($listData = false)
    {
        $p = [
            'query' => $this->getQuery($listData),
            'pagination' => [
                'pageSize' => Yii::$app->params['videoPageSize']
            ],
            'sort' => [
                'defaultOrder' => [
                    $this->SortBy => intval($this->SortDirect)
                ],
            ]
        ];
        if ($this->isSearchText()) {
            $p['sort']['defaultOrder'] = [];
        }
        return new ActiveDataProvider($p);
    }
}