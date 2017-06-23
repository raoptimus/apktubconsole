<?php
/**
 * Created by IntelliJ IDEA.
 * User: ra
 * Date: 21.05.15
 * Time: 21:05
 */
namespace app\components;

use Yii;
use yii\base\Model;
use yii\validators\Validator;

class EmbedDocListValidator extends Validator
{
    public $model;
    public $skipOnEmpty = false;
    public $errAttributes;
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->message === null) {
            $this->message = Yii::t('yii', '{attribute} is invalid.');
        }
    }

    /**
     * Validates a single attribute.
     * Child classes must implement this method to provide the actual validation logic.
     *
     * @param \yii\mongodb\ActiveRecord $object the data object to be validated
     * @param string $attribute the name of the attribute to be validated.
     */
    public function validateAttribute($object, $attribute)
    {
        $errAttr = $attribute;
        if (!empty($this->errAttributes)) {
            if (is_array($this->errAttributes)) {
                $i = array_search($attribute, $this->attributes);
                if (array_key_exists($i, $this->errAttributes)) {
                    $errAttr = $this->errAttributes[$i];
                }
            } else {
                $errAttr = $this->errAttributes;
            }
        }
        $list = $object->{$attribute};

        if ($this->isEmpty($list)) {
            $this->addError($object, $errAttr, "{attribute} can't be empty");
            return;
        }
        if (!is_array($list)) {
            $this->addError($object, $errAttr, '{attribute} should be an array');
            return;
        }

        foreach ($list as $key => $docAttr) {
            if (!is_array($docAttr)) {
                $this->addError($object, "{$errAttr}[{$key}]", '{attribute} should be an array');
                return;
            }
            /**
             * @var $model Model
             */
            $model = new $this->model;
            $model->scenario = $object->scenario;
            $model->setAttributes($docAttr);

            if (!$model->validate()) {
                foreach ($model->getErrors() as $errorAttr) {
                    foreach ($errorAttr as $err) {
                        $this->addError($object, "{$errAttr}[{$key}]", $err);
                    }
                }
            }
        }
    }
}