<?php

namespace app\components;

use kartik\typeahead\TypeaheadBasic;
use kartik\typeahead\TypeaheadBasicAsset;
use yii\helpers\Json;
use yii\web\View;
use yii\web\J...pression;

class MyTypeAhead extends TypeaheadBasic {
    /**
     * Registers the needed assets
     */
    public function registerAssets()
    {
        $view = $this->getView();
        TypeaheadBasicAsset::register($view);

        $this->registerPluginOptions('typeahead');
        $data = Json::encode(array_values($this->data));
        $dataVar = 'kvTypData_' . hash('crc32', $data);
        $view->registerJs("var {$dataVar} = {$data};", View::POS_HEAD);
        $this->dataset['name'] = $dataVar;
        if (!isset($this->dataset['source'])) {
            $this->dataset['source'] = new J...pression('kvSubstringMatcher(' . $dataVar . ')');
        }
        $id = 'jQuery("#' . $this->options['id'] . '")';
        $dataset = Json::encode($this->dataset);

        $js = "var \$myTextarea = jQuery('#prettyTags');
        {$this->_hashVar}.updater = function(item) {
            alert(321);
        }";
        $view->registerJs($js, View::POS_HEAD);

/*        \$myTextarea.append(item, ' ');
        return '';*/


        $js = "{$id}.typeahead({$this->_hashVar}, {$dataset});";
        $view->registerJs($js);
        $this->registerPluginEvents($view);
    }


    /**
     * Generates a hashed variable to store the pluginOptions. The following special data attributes
     * will also be setup for the input widget, that can be accessed through javascript :
     * - 'data-krajee-{name}' will store the hashed variable storing the plugin options. The {name}
     *   tag will represent the plugin name (e.g. select2, typeahead etc.) - Fixes issue #6.
     *
     * @param string $name the name of the plugin
     */
    protected function hashPluginOptions($name)
    {
        echo('<pre>');
        print_r($this->pluginOptions);
        exit;
        $this->_encOptions = empty($this->pluginOptions) ? '' : Json::htmlEncode($this->pluginOptions);
        $this->_hashVar = $name . '_' . hash('crc32', $this->_encOptions);
        $this->options['data-krajee-' . $name] = $this->_hashVar;
    }
}