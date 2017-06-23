<?php
/**
 * Created by IntelliJ IDEA.
 * User:
 * Date: 11.02.16
 * Time: 17:05
 */

namespace app\controllers;

use Yii;
use yii\web\HttpException;
use yii\web\Response;
use app\models\traffic\Statistic;

class ReportController extends AccController
{

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access']['rules'][0]['roles'] = ['Admin', 'Manager'];
        return $behaviors;
    }


    public function actionIndex($monthStart = null, $monthEnd = null)
    {
        $this->pageTitle = 'Statistic from google analytics';

        $monthStart = $monthStart ? : date('Y-m', strtotime('first day of previous month'));
        $monthEnd = $monthEnd ?: date('Y-m');

        $metricList = Statistic::getStatistic(date('Y-m', strtotime($monthStart)), date('Y-m', strtotime($monthEnd)));

        // export  excel
        if (!empty(Yii::$app->request->get('xls'))) {
            $this->xl...port($metricList);
        }

        // export  csv
        if (!empty(Yii::$app->request->get('csv'))) {
            $this->csvExport($metricList);
        }

        if (!is_array($metricList) || empty($metricList)) {
             throw new HttpException(403, Yii::t('yii',"Нет статистики, для этого периода запустить:php yii google/index {$monthStart} {$monthEnd}"));
        }

        return $this->render('index', [
            'statisticList' => $metricList,
            'monthStart' => $monthStart,
            'monthEnd' => $monthEnd,
            'debug' => true,
        ]);
    }

    private function xl...port($metricList)
    {
        $this->layout = false;

        $content = $this->render('_list', [
            'statisticList' => $metricList,
            'debug' => false,
        ]);

        Yii::$app->response->sendContentAsFile(
            mb_convert_encoding($content , "HTML-ENTITIES", "UTF-8"),
            'analytics.xls',
            ['mimeType' => 'application/vnd.ms-excel']
        );

        Yii::$app->end();
    }

    /**
     * Преобразуем из HTMl в csv
     * @param $metricList
     * @throws HttpException
     * @throws \yii\base\ExitException
     */
    public function csvExport($metricList)
    {
        $this->layout = false;
        $content  =   $this->renderAjax('_list', [
            'statisticList' => $metricList,
            'debug' => false,
        ]);
        $response = Yii::$app->getResponse();
        $content = mb_convert_encoding($content , "HTML-ENTITIES", "UTF-8");
        $html = simplexml_load_string ($content);

        // парсим векрхний заголовок таблицы
        $xpath = $html->xpath('//thead[1]/tr')[0];
        $inc = 1;
        foreach ($xpath as $item) {
            $podList[] = $this->convertFromXml($item);
            // добавляем пустые строки
            if ($inc > 2) {
                $podList = array_merge($podList, array_fill(0, 3, null));
            }

            $inc++;
        }
        array_pop($podList);
        $dataList[] = $podList;

        // парсим нижний заголовок таблицы
        $xpath = $html->xpath('//thead[2]/tr')[0];
        $podList =[];
        foreach ($xpath as $item) {
            $podList[] =  $this->convertFromXml($item);
        }
        $dataList[] = $podList;

        // парсим остальные данные
        $xpath = $html->xpath('//tbody/tr');
        foreach ($xpath as  $item) {
            $podList = [];
            foreach ($item as $tr) {
                $result =  $this->convertFromXml($tr);
                $podList[] = $result;
            }
            // небольхой хак для дат
            if (count($item) == 1) {
                $podList = array_merge($podList, array_fill(0, 16, null));
            } else {
                array_unshift($podList, null);
            }
            $dataList[] = $podList;
        }


        $fp = fopen('php://temp', 'w');
        foreach ($dataList as $fields) {
            fputcsv($fp, $fields, ';', '"');
        }

        $response->sendStreamAsFile($fp, 'analytics.csv', [
            'Content-type' => 'application/octet-stream; charset=UTF-8',
        ]);


        Yii::$app->end();
    }


    /**
     * Конверитруем из Xml в массив
     *
     * @param \SimpleXMLElement $xml
     *
     * @return array
     */
    private function convertFromXml(\SimpleXMLElement $xml)
    {
        return json_decode(json_encode((array) $xml), true)[0];;
    }
}
