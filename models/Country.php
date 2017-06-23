<?php

namespace app\models;

use app\components\MongoActiveRecord;
use Yii;

/**
 * This is the model class for collection Country.
 *
 * @property string $_id
 * @property string $Name
 * @property string $Code
 * @property string $Region
 */
class Country extends MongoActiveRecord
{

    /**
     * Default country code, used if geoip return nil or country not exists in collection
     */
    const UNKNOWN = "UN";

    /**
     * @return string the name of the index associated with this ActiveRecord class.
     */
    public static function collectionName()
    {
        return 'Country';
    }

    public static function getCountiesList()
    {
        $countries = self::find()->all();
        $returnArray = [];
        foreach ($countries as $country) {
            if (empty($country['Region'])) {
                $returnArray[(string)$country['Code']] = $country['Name'];
            } else {
                $region = (string)$country['Region'];
                if (empty($returnArray[$region])) {
                    $returnArray[$region] = [
                        'items' => [],
                        'title' => (string)$country['RegionTitle'],
                    ];
                }
                $returnArray[$region]['items'][(string)$country['Code']] = $country['Name'];
            }
        }
        array_walk($returnArray, function(&$region) {
            if (is_array($region)) {
                asort($region['items']);
            }
            return $region;
        });
        return $returnArray;
    }

    public static function getAllCountiesCodes()
    {
        $returnArray = [self::UNKNOWN];
        foreach (self::find()->all() as $country) {
            $returnArray[] = (string)$country['Code'];
        }
        return $returnArray;
    }

    public static  function getCodeTitleList() {
        $returnArray = [];
        foreach (self::find()->all() as $country) {
            $returnArray[(string)$country['Code']] = (string)$country['Name'] . " ({$country['Code']})" ;
        }
        return $returnArray;
    }

    /**
     * @return array list of attribute names.
     */
    public function attributes()
    {
        return ['_id', 'Name', 'Code', 'Region', 'RegionTitle'];
    }
}
