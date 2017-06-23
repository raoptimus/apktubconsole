<?php

namespace app\models;

use app\components\Enum;

class CommentStatus extends Enum
{
    static $_values = [
        'Approved',
        'Spam'
    ];
}