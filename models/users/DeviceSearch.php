<?php

namespace app\models\users;

use Yii;
use yii\data\ActiveDataProvider;

/**
 * DailyStatSearch represents the model behind the search form about `app\models\DailyStat`.
 */
class DeviceSearch extends Device
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    '_id',
                    'Manufacture',
                    'Model',
                    'Os',
                    'VerOs',
                    'Source.Site',
                    'Source.Landing',
                    'Source.Ad',
                    'Source.Apk',
                    'Source.Ver',
                    'LastIp',
                    'LastGeo.countryname',
                    'HasGoogleId',
                ],'safe']
        ];
    }

    public function formName()
    {
        return "f";
    }

    public function search($params)
    {
        $query = Device::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => $this->attributes(),
                'defaultOrder' => [
                    'LastActiveTime'=> SORT_DESC
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
            ->andFilterWhere(['like', 'Manufacture', $this->Manufacture])
            ->andFilterWhere(['like', 'Model', $this->Model])
            ->andFilterWhere(['like', 'Os', $this->Os])
            ->andFilterWhere(['like', 'VerOs', $this->VerOs])
            ->andFilterWhere(['like', 'Source.Site', $this->{'Source.Site'}])
            ->andFilterWhere(['like', 'Source.Landing', $this->{'Source.Landing'}])
            ->andFilterWhere(['like', 'Source.Ad', $this->{'Source.Ad'}])
            ->andFilterWhere(['like', 'Source.Apk', $this->{'Source.Apk'}])
            ->andFilterWhere(['like', 'Source.Ver', $this->{'Source.Ver'}])
            ->andFilterWhere(['like', 'LastIp', $this->LastIp]);

        if ($this->HasGoogleId == 'true') {
            $query->andWhere(['HasGoogleId' => true]);
        } elseif ($this->HasGoogleId == 'false') {
            $query->andWhere(['HasGoogleId' => false]);
        } elseif ($this->HasGoogleId == 'null') {
            $query->andWhere(['HasGoogleId' => null]);
        }

        return $dataProvider;
    }
}