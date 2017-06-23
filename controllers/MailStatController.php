<?php

namespace app\controllers;

use app\models\Crypto;
use Yii;
use yii\web\NotFoundHttpException;
use app\models\MailStat;
use app\models\users\AppUser;

class MailStatController extends AccController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        return $behaviors;
    }

    public function actionIndex($email,$secret)
    {
        $own_secret = md5('supersecret' . date('d-m-Y'));

        if ($secret != $own_secret) {
            throw new NotFoundHttpException('Requested page was not found on server');
        } else {
            $stat = new MailStat();
            $stat->email = $email;
            $stat->date = new \MongoDate(time());
            $stat->type = 'return';
            $stat->save();
            return true;
        }
    }

    public function actionNew($email,$secret)
    {
        $own_secret = md5('supersecret' . date('d-m-Y'));

        if ($secret != $own_secret) {
            throw new NotFoundHttpException('Requested page was not found on server');
        } else {
            $stat = new MailStat();
            $stat->email = $email;
            $stat->date = new \MongoDate(time());
            $stat->type = 'new';
            $stat->save();
            return true;
        }
    }

    public function actionUnsubscribe($email) {
        $crypto = new Crypto();
        $email = $crypto->decrypt($email);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            \Yii::warning('Can\'t unsubscribe ' . $email,'MailController');
            return 1;
        }

        $user = AppUser::find()
            ->where(['Email' => $email])
            ->one();

        if (!$user) {
            \Yii::warning('Can\'t find ' . $email,'MailController');
            return 1;
        }
        $user->{'mail.Subscribe'} = false;
        $user->save();
        return 0;
    }}
