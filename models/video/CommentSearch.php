<?php

namespace app\models\video;

use app\models\users\AppUser;
use Yii;
use app\components\ActiveDataProviderExternalTitles;

/**
 * DailyStatSearch represents the model behind the search form about `app\models\DailyStat`.
 */
class CommentSearch extends Comment
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
                    'VideoId',
                    'UserId',
//                    'Body',
                    'Status',
//                    'PostDate',
                    'Language',
                ],
                'safe'
            ]
        ];
    }

    public function formName()
    {
        return "f";
    }

    public function search($params)
    {
        $query = Comment::find();

        $dataProvider = new ActiveDataProviderExternalTitles([
            'query' => $query,
            'externalAttributes' => ['VideoId','UserId'],
            'sort' => [
                'attributes' => [
                    '_id',
                    'VideoId',
                    'UserId',
                    'Status',
                    'PostDate',
                    'Language',
                ],
                'defaultOrder' => [
                    'PostDate'=> SORT_DESC
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


        if (!empty($this->_id)) {
            $query->andFilterWhere(['_id' => intval($this->_id)]);
        }

        if (!empty($this->VideoId)) {
            $videoIds = [];
            if (is_numeric($this->VideoId)) {
                $videoIds[] = intval($this->VideoId);
            } else {
                $videos = Video::find()->where(['like','Title.Quote',$this->VideoId])->select(['_id'])->all();

                foreach ($videos as $video) {
                    $videoIds[] = intval($video->_id);
                }
            }
            $query->andFilterWhere(['VideoId' => $videoIds]);
            unset($videos);
        }

        if (!empty($this->UserId))  {
            $userIds = [];
            if (is_numeric($this->UserId)) {
                $userIds[] = intval($this->UserId);
            } else {
                $users = AppUser::find()->where(['like','UserName',$this->UserId])->select(['_id'])->all();
                foreach ($users as $user) {
                    $userIds[] = intval($user->_id);
                }
            }
            $query->andFilterWhere(['UserId' => $userIds]);
        }

        if ($this->Status !== '') {
            $query->andFilterWhere(['Status' => intval($this->Status)]);
        }

        $query->andFilterWhere(['Language' => $this->Language]);

        return $dataProvider;
    }
}