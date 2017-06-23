<?php
/**
 * Created by IntelliJ IDEA.
 * User: ra
 * Date: 11.06.15
 * Time: 16:56
 */

namespace app\models\push;


use app\components\MongoActiveRecord;
use app\models\Application;
use Yii;

/**
 * Class NotifyUpgrade
 * @property string Ver
 * @package app\models\push
 */
class NotifyUpgrade extends MongoActiveRecord
{
    private $_versions;
    private function versions()
    {
        if (!empty($this->_versions)) {
            return $this->_versions;
        }
        $lastBuild = Application::find()->orderBy(['Ver' => SORT_DESC])->limit(1)->one();
        $ver = floatval($lastBuild->Ver);
        $rver = round($ver, 1);
        $this->_versions = array_unique([$rver - 0.1, $rver, $ver, $rver + 0.1, $rver + 0.2]);
        return $this->_versions;
    }

    /*
     * override methods
     */
    public function init()
    {
        $this->Ver = $this->versions()[2];
    }

    public function attributeHints()
    {
        return [
            'Ver' => 'Версия приложения о выходе которой нужно оповестить пользователей',
        ];
    }

    public function attributeLabels()
    {
        return [
            'Ver' => Yii::t("dict", "Version"),
        ];
    }

    public function rules()
    {
        return [
            [
                'Ver',
                'in',
                'range' => $this->versions(),
                'message' => Yii::t('yii', '{attribute} is invalid.') . ' Only ' . implode(", ", $this->versions()),
            ],
            [
                'Ver', 'required'
            ]
        ];
    }

    public function attributes()
    {
        return [
            'Ver'
        ];
    }

}