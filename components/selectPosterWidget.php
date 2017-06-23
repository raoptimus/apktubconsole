<?php
namespace app\components;

use yii\base\Widget;
use yii\helpers\Html;

class selectPosterWidget extends Widget{
    public $pictures;
    public $id;

    public function run(){
        $returnString = "";
        foreach($this->pictures as $picture) {
            $returnString .= Html::tag(
                'option',
                "Image #" . $picture['index'],
                [
                    'value' => $picture['index'],
                    'data-img-src' => $picture['src'],
                    'selected' => $picture['selected']
                ]
            );
        }
        return Html::tag('select',$returnString,['class'=>'image-picker', 'id'=> 'image-picker-' . $this->id]);
    }
}
?>
