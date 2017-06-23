<?php
namespace app\commands;
use app\models\Crypto;
use app\models\users\Device;
use yii\console\Controller;
use app\models\users\AppUser;
use Yii;

class MailController extends Controller
{
    /**
     * This command will send message to a new member
     * @param string $id is _id of AppUser
     * @return int Exit Code
     */
    public function actionNewMember($id)
    {
        $user = AppUser::findOne(['_id' => intval($id),'mail.Subscribe' => ['$ne' => false]]);
        if (is_null($user)) {
            \Yii::warning('Could not find user with active subscribe','MailController');
            return 1;
        }
        if (empty($user->Email)) {
            \Yii::warning('User has empty email address','MailController');
            return 1;
        }
        $statFileName = base64_encode(json_encode(['email' => $user->Email, 'type' => 'new'])) . '.png';
        \Yii::$app->mailer->compose('newMember',['site' => '...apk....','statFileName' => $statFileName])
            ->setFrom('...apk@...apk....')
            ->setTo($user->Email)
            ->setSubject('Добро пожаловать в ...apk....!')
            ->send();
        return 0;
    }

    /**
     * This command will send message to a new all members
     * @return int Exit Code
     */
    public function actionAllNewMembers() {
        $users = AppUser::find()->where(['mail.welcomeSended' => ['$ne' => true], 'Email' => ['$exists' => true, '$ne' => '']])->limit(100)->all();

        $messages = [];
        foreach ($users as $user) {
            $statFileName = base64_encode(json_encode(['email' => $user->Email, 'type' => 'new'])) . '.png';
            $crypto = new Crypto();
            $unsubscribeLink = '/unsubscribe.php?email=' . $crypto->crypt($user->Email);
            $messages[] = \Yii::$app->mailer->compose('newMember',['site' => '...apk....','statFileName' => $statFileName,'unsubscribeLink' => $unsubscribeLink])
                ->setFrom('...apk@...apk....')
                ->setTo($user->Email)
//                ->setTo(['anton@jabberz.net','sainomori@gmail.com'])
                ->setSubject('Добро пожаловать в ...apk....!');
            $user->{'mail.welcomeSended'} = true;
            $user->save();
        }
        try {
            \Yii::$app->mailer->sendMultiple($messages);
        } catch (\Exception $e) {
            \Yii::warning('Error while sending email ' . $e->getMessage(),'MailController');
            return 1;
        }
        return 0;
    }

    /**
     * This command will send message to a new all members
     * @param int $gt GreaterThen days away
     * @param int $lt LesserThen days away
     * @return int Exit Code
     * @throws \Exception incase of empty param
     */
    public function actionReturnMembers($gt,$lt=0) {
        $testSend = true;
        if (empty($gt)) {
            throw new \Exception('There must be a $gt param');
        }

        $date_condition = [];
        $date_condition['$lt'] = new \MongoDate(strtotime("-$gt day"));
        if (!empty($lt)) {
            $date_condition['$gt'] = new \MongoDate(strtotime("-$lt day"));
        }

        while ($devices = Device::find()
            ->select(['_id'])
            ->where(['or', ['LastActiveTime' => $date_condition],['LastActiveTime' => ['$exists' => false]]])
            ->andWhere(['or',['mail.LastReturnMail' => ['$exists' => false]],['mail.LastReturnMail' => ['$lt' => new \MongoDate(strtotime("-$gt day"))]]])
            ->orderBy(['_id' => -1])
            ->limit(70)
            ->all()
        ){
            $tokens = [];
            foreach($devices as $device) {
                $tokens[] = $device->_id;
                $device->{'mail.LastReturnMail'} = new \MongoDate(time());
                $device->save();
            }

            $users = AppUser::find()
                ->where(['Tokens' => $tokens])
                ->andWhere(['mail.Subscribe' => ['$ne' => false]])
                ->andWhere(['Email' => ['$exists' => true, '$ne' => '']])
                ->all();

            $messages = [];
            foreach ($users as $user) {
                echo ($user->Email . "\n");
                if (filter_var($user->Email, FILTER_VALIDATE_EMAIL)) {
                    $statFileName = base64_encode(json_encode(['email' => $user->Email, 'type' => 'return'])) . '.png';
                    $crypto = new Crypto();
                    $unsubscribeLink = '/unsubscribe.php?email=' . $crypto->crypt($user->Email);
                    $messages[] = \Yii::$app->mailer->compose('returnMail',[
                        'site' => Yii::$app->params['apkSite'],
                        'statFileName' => $statFileName,
                        'unsubscribeLink' => $unsubscribeLink,
                        'apkLink' => Yii::$app->params['apkMailLink']])
                        ->setFrom('...apk@...apk....')
                        ->setTo($user->Email)
//                        ->setTo(['anton@jabberz.net','sainomori@gmail.com','haffaz.apk@gmail.com'])
                        ->setSubject('Вы давно не заходили, а у нас много нового!');

/*                    if ($testSend) {
                        $messages[] = \Yii::$app->mailer->compose('returnMail',[
                            'site' => Yii::$app->params['apkSite'],
                            'statFileName' => $statFileName,
                            'unsubscribeLink' => $unsubscribeLink,
                            'apkLink' => Yii::$app->params['apkMailLink']])
                            ->setFrom('...apk@...apk....')
                            ->setTo(['anton@jabberz.net','sainomori@gmail.com'])
                            ->setSubject('Вы давно не заходили, а у нас много нового!');
                        $testSend = false;
                    }*/
                    $user->{'mail.LastReturnMail'} = new \MongoDate(time());
                    $user->save();
                }
            }
            echo ("Sending\n");
            try {
                \Yii::$app->mailer->sendMultiple($messages);
            } catch (\Exception $e) {
                echo ("ERROR! Retying.\n");
                \Yii::$app->mailer->getTransport()->stop();
                \Yii::$app->mailer->getTransport()->start();
                \Yii::$app->mailer->sendMultiple($messages);
            }
            echo ("Sent\n");
        }
        return 0;
    }
}