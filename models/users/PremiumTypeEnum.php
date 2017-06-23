<?php

namespace app\models\users;

use app\components\Enum;

class PremiumTypeEnum extends Enum
{
    protected static $_values = [
        'none',
        'trial',
        'signup',
        'rebill',
    ];
}