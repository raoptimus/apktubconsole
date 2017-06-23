<?php
/**
 * Created by IntelliJ IDEA.
 * User: ra
 * Date: 03.06.15
 * Time: 11:05
 */

namespace app\models\push;


use app\components\Enum;

class State extends Enum
{
    static $_values = [
        "Wait",
        "InProgress",
        "Error",
        "Finish",
    ];
}