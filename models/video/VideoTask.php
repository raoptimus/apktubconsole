<?php
namespace app\models\video;

use Yii;
use yii\base\Model;

class VideoTask extends Model
{
    public $Id;
    public $Status;
    public $Progress;
    public $Name;
    public $Server;
    public $Pid;
    public $Started;
    public $Created;
    public $Frontends;
    public $Errors;
}