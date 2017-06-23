<?php

namespace app\models\users;

use app\components\Enum;

/**
 * Class DeviceEventActionEnum
 * @package app\models\users
 * @property int FLaunch
 * @property int Launch
 * @property int ReInstall
 * @property int Update
 */
class DeviceEventActionEnum extends Enum
{
    protected static $_values = [
        'Launch',
        'FLaunch',
        'ReInstall',
        'Update',
    ];
}