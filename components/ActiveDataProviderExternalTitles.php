<?php

namespace app\components;
use yii\data\ActiveDataProvider;

class ActiveDataProviderExternalTitles extends ActiveDataProvider {
    public $externalAttributes;
    /**
     * @inheritdoc
     */
    protected function prepareModels()
    {
        $models = parent::prepareModels();

        if (empty($models)) {
            return [];
        }

        if (count($this->externalAttributes)) {
            $rawList = [];
            foreach ($models as $model) {
                foreach ($this->externalAttributes as $enAttr) {
                    if (isset($model[$enAttr]) && !empty($model[$enAttr])) {
                        $rawList[$enAttr][] = $model[$enAttr];
                    }
                }
            }
            $resultsList = [];

            $class = get_class($models[0]);

            foreach ($rawList as $enAttr => $enAttrValues) {
                $resultsList[$enAttr] = call_user_func($class . '::get'.$enAttr.'Titles', $enAttrValues);
            }

            foreach ($models as &$model) {
                foreach ($this->externalAttributes as $enAttr) {
                    $model->{$enAttr . 'Title'} = $resultsList[$enAttr][$model[$enAttr]];
                }
            }

            unset($model);
            unset($rawList);
            unset($resultsList);
        }
        return $models;
    }
}