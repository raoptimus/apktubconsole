<?php

namespace app\models\video;

use app\models\Language;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class CategorySearch extends Model
{
    public $Search;
    public $Language = "ru";

    public function formName()
    {
        return "form";
    }

    /**
     * @return array list of attribute names.
     */
    public function attributes()
    {
        return [
            'Search',
            'Language',
            'Title'
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Search' => Yii::t('dict', 'Search'),
            'Language' => Yii::t('dict', 'Language'),
        ];
    }

    public function rules()
    {
        return [
            [
                'Search',
                'string',
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

    /**
     * @return \yii\mongodb\ActiveQuery|\yii\mongodb\Query
     */
    public function getQuery()
    {
        if (is_numeric($this->Search)) {
            $id = intval($this->Search);
            if ($id == $this->Search) {
                return Category::find()->andWhere(["_id" => $id]);
            }
        }

        if (empty($this->Search)) {
            $query = Category::find();
        } else {
            $query = Category::findText($this->Search);
        }

        return $query;
    }

    /**
     * @return ActiveDataProvider
     */
    public function getDataProvider()
    {
        return new ActiveDataProvider([
            'query' => $this->getQuery(),
            'pagination' => [
                'pageSize' => Yii::$app->params['categoryPageSize']
            ],
            'sort' => [
                'attributes' => [
                    '_id',
                    'Title'
                ]
            ]
        ]);
    }
}