<?php
/**
 * Created by IntelliJ IDEA.
 * User: ra
 * Date: 08.05.15
 * Time: 21:42
 */

namespace app\components;

use yii\bootstrap\Alert;
use Yii;

class MyHtml extends \yii\helpers\Html {
    /**
     *
     * @param $icon
     * @param array $options
     * @return string
     */
    public static function glyphicon($icon, $options = [])
    {
        $c = [
            "glyphicon",
            "glyphicon-" . $icon,
        ];
        if (!empty($options)) {
            if (isset($options["class"])) {
                $c = array_merge($c, explode(" ", $options["class"]));
                $c = array_unique($c);

            }
        }

        $options['class'] = implode(" ", $c);
        return self::tag("i", "", $options);
    }

    /**
     * @return string bootstrap alert with flash messages
     */
    public static function alert()
    {
        $types = ['success','info','warning','danger'];
        foreach(Yii::$app->session->getAllFlashes() as $key => $message)
        {
            if (empty($message)) {
                continue;
            }
            if (!in_array($key, $types)) {
                if ($key == "error") {
                    $key = "danger";
                } else {
                    $key = $types[1];
                }
            }
            return Alert::widget([
                'options' => [
                    'class' => 'alert-' . $key,
                    'id' => 'alert',
                ],
                'body' => self::tag("b", ucfirst($key) . "! ") . $message,
            ]);

        }
    }
}