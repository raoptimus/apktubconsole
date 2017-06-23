<?php

namespace app\models\users;

use Yii;
use yii\data\ActiveDataProvider;

/**
 * DailyStatSearch represents the model behind the search form about `app\models\DailyStat`.
 *
 * @property string UserName
 * @property string Email
 * @property int _id
 * @property string Tel
 * @property string PremiumType
 */
class AppUserSearch extends AppUser
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                ['UserName', 'Email'],
                'string',
                'on' => 'search',
            ],
            [
                ['_id'],
                'integer',
                'on' => 'search',
            ],
            [
                'PremiumType',
                'in',
                'range' => PremiumTypeEnum::getValues(),
                'message' => Yii::t('dict', 'Field is invalid.'),
                'on' => 'search',
            ],
            [['_id', 'UserName', 'Tel', 'PremiumType', 'Email'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            '_id' => 'Id',
            'UserName' => Yii::t('dict', 'User name'),
            'Email' => Yii::t('dict', 'User email'),
            'PremiumType' => Yii::t('dict', 'Premium status'),
        ];
    }

    public function formName()
    {
        return "f";
    }

    public function search($params)
    {
        $query = AppUser::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => $this->attributes(),
                'defaultOrder' => [
                    '_id' => SORT_DESC
                ]
            ],
            'pagination' => [
                'pageSize' => Yii::$app->params['appUserPageSize'],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['_id' => $this->_id])
            ->andFilterWhere(['like', 'UserName', $this->UserName])
            ->andFilterWhere(['like', 'Email', $this->Email])
            ->andFilterWhere(['like', 'Tel', $this->Tel]);

        if (!is_null($this->PremiumType) && $this->PremiumType != -1) {
            $query->andFilterWhere(['Premium.Type' => PremiumTypeEnum::getValue($this->PremiumType)]);
        }
        return $dataProvider;
    }

    public function attributes()
    {
        return [
            '_id',
            'UserName',
            'Email',
            'Tel',
            'PremiumType',
            'CreationDate',
            'Language'
        ];
    }
}