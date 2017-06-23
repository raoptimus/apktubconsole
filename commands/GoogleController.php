<?php

namespace app\commands;

use app\models\traffic\TrafficStatistic;
use Yii;
use yii\console\Controller;
use app\components\google\Analytics;
use yii\helpers\Console;
use app\models\traffic\Statistic;

class GoogleController extends Controller
{

    const CACHE_DURATION_PROFILE = (60 * 60  * 24 * 30);

    /**
     * This command run get statistic from google
     *      * and put in the cache
     * command for get traffic from mongo:
     *   db.TrafficStatistic.find({monthStart: {$gte:'2015-01', $lte:'2015-02'}}).sort({date: 1}).pretty();
     */
    public function actionIndex()
    {
        $analytics = $this->getInstance();

        $currentDay = date('d');

        $updateMonth = date('Y-m');
        $data = $this->getMetricList($analytics, $updateMonth, $updateMonth);
        // если месяц новый создаем новую запись
        if (!$this->findModel($updateMonth)) {
            $this->saveData($data, $updateMonth, $updateMonth);
        } else {
            $this->updateData($data, $updateMonth);
        }

        // если месяц новый то обновляем статистику за пердыдущий месяц
        // т. к статистика собирается в google  с опозданием на 2 дня
        if ($currentDay <= 3) {
            $prevMonth  = date("Y-m", strtotime("-1 months"));
            Console::output("Statistic update previous month $prevMonth\n");
            $data = $this->getMetricList($analytics, $prevMonth, $prevMonth);
            $this->updateData($data, $prevMonth);
        }

    }

    /**
     * Собраем стистику с начала 15 года
     * Необходимо запустить если коллеция очищена
     *
     * @param $analytics
     * @return bool
     */
    protected function getPreviousYear($analytics)
    {
        $d1 = new \DateTime("2015-01");
        $d2 = new \DateTime(date('Y-m') .'-03');
        // получем количество месяцев прошедких января 2015 года + 1 на текущий месяц
        $monthCount = $d1->diff($d2)->m + ($d1->diff($d2)->y * 12) + 1;
        // если хоть раз сохранили запись новая
        $isSave = false;
        $year = 2015;
        $index = 0;
        for ($i = 1; $i <=$monthCount; $i++) {
            $index++;
            // за каждый год сбасываем месяц
            if ($index == 13) {
                $index = 1;
                $year++;
            }

            $indexEnd = ($index < 10) ? '0' . $index : $index;
            $monthStart = "$year-$indexEnd";

            // сохраняем, если записи еще нет
            if (!$this->findModel($monthStart)) {
                Console::output("Run get statistic $monthStart $monthStart\n");
                $data = $this->getMetricList($analytics, $monthStart, $monthStart);
                $this->saveData($data, $monthStart, $monthStart);
                $isSave = true;
            }

        }

        return $isSave;
    }




    protected function getProfileList(\Google_Service_Analytics $analytics)
    {
        $cacheKey = 'profileAnalyticsList2';
        $cache = Yii::$app->cache->get($cacheKey);

        if ($cache === false) {
            $service = new Analytics();
            $profileList = $service->getFirstProfileId($analytics);
            if (!Yii::$app->cache->set($cacheKey, $profileList, self::CACHE_DURATION_PROFILE)) {
                throw new Yii\base\ErrorException('Error set '.$cacheKey);
            }

            return $profileList;
        }

        return $cache;

    }



    protected function getInstance()
    {
        $config  = Yii::$app->params['google']['analytics'];
        $secret = file_get_contents($config['file_path_secret']);
        $google = new Analytics();
        $analytics = $google->getAnalytics($config['email'], $secret);

        return $analytics;
    }



    /**
    @desc example return format
    [http://m.xyu....] => Array
    (
           [name] => http://m.xyu....
            [data] => Array
                (
                    [desktop] => Array
                        (
                            [0] => Array
                                (
                                    [0] => 0000
                                    [1] => 18998
                                    [2] => 237107
                                    [3] => 237342
                                    [4] => 9839
                                )

                        )

                    [mobile] => Array
                        (
                            [0] => Array
                                (
                                    [0] => 0001
                                    [1] => 5165630
                                    [2] => 78252291
                                    [3] => 78965162
                                    [4] => 2567880
                                )

                        )

                    [tablet] => Array
                        (
                            [0] => Array
                                (
                                    [0] => 0001
                                    [1] => 271956
                                    [2] => 3030493
                                    [3] => 3049232
                                    [4] => 152428
                                )

                        )

                )

        )

    **/
    protected function getMetricList(\Google_Service_Analytics $analytics, $monthStart, $monthEnd)
    {
        $dimensionsValue = 'ga:users,ga:pageviews,ga:sessions,ga:bounceRate';
        $dataList = [];
        $deviceType = [
            'desktop',
            'mobile',
            'tablet'
        ];

            $profileList = $this->getProfileList($analytics);
            foreach ($profileList as $profile) {
                $deviceMetricList =[];
                $ga = $profile['profileId'];

                // перебираем типы устрройств
                foreach ($deviceType as  $value) {
                    $optParams = [
                        'dimensions' => 'ga:nthMonth',
                        'sort' => 'ga:nthMonth',
                        'filters' => 'ga:deviceCategory==' . $value,
                    ];

                    // получаем последний день месяца
                    $monthEnd = (new \DateTime($monthEnd))->format( 'Y-m-t' );
                    // from: https://developers.google.com/analytics/devguides/reporting/core/dimsmets
                    $statistic = $analytics->data_ga->get("ga:{$ga}", "{$monthStart}-01", $monthEnd, $dimensionsValue, $optParams);
                    $dataRows = $statistic->getRows()[0];

                    $lists = [
                        'users' => (int) $dataRows[1],
                        'pageviews' => (int) $dataRows[2],
                        'sessions' => (int) $dataRows[3],
                        'bounceRate' => (int) $dataRows[4],
                    ];

                    $deviceMetricList[$value] = $lists;

                }

                $dataList[] = [
                    'name' => $profile['name'],
                    'ga' =>  $ga,
                    'data' => $deviceMetricList,
                ];


            }

            return $dataList;

    }


    protected function saveData($dataList = [], $monthStart, $monthEnd)
    {
        $stat = new Statistic();
        $stat->monthStart = $monthStart;
        $stat->_id = md5($monthStart) ;
        $stat->monthEnd = $monthEnd;
        $stat->date = new \MongoDate(time());
        $stat->type = 'google';
        $stat->statistic = $dataList;

        return $stat->save();
    }

    protected function updateData($dataList = [], $monthStart)
    {
        Console::output("Update  statistic $monthStart\n");

        $model = $this->findModel($monthStart);
        $model->statistic = $dataList;

        if (!$model->save(false)) {
            print_r($model->getErrors());
        }

    }

    /**
     * Finds the Statistic model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Statistic the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($monthStart)
    {
        if (($model = Statistic::find()->where(['_id'=> md5($monthStart)])->one()) !== null) {
            return $model;
        } else {
            return false;
        }
    }

}
