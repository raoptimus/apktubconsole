<?php
namespace app\commands;

use app\models\User;
use Yii;
use yii\console\Controller;

class RbacController extends Controller
{
    /**
     * getting rules for RBAC
     */
    public function actionInit()
    {
        $auth = Yii::$app->authManager;

        //А затем...
        //permissions
        $pMember = $auth->createPermission('beMember');
        $pMember->description = 'Разрешения пользователя';
        $auth->add($pMember);

        $pVideoManager = $auth->createPermission('beVideoManager');
        $pVideoManager->description = 'Разрешения видео-менеджера';
        $auth->add($pVideoManager);

        $pPartner = $auth->createPermission('bePartner');
        $pPartner->description = 'Разрешения партнёра';
        $auth->add($pPartner);

        $pManager = $auth->createPermission('beManager');
        $pManager->description = 'Разрешения менеджера';
        $auth->add($pManager);

        $pAdmin = $auth->createPermission('beAdmin');
        $pAdmin->description = 'Разрешение админа';
        $auth->add($pAdmin);


        //roles
        $rMember = $auth->createRole('Member');
        $rMember->description = 'Авторизованные пользователи';
        $auth->add($rMember);

        $rPartner = $auth->createRole('Partner');
        $rPartner->description = 'Роль партнёра';
        $auth->add($rPartner);

        $rVideoManager = $auth->createRole('VideoManager');
        $rVideoManager->description = 'Роль видео-менеджера';
        $auth->add($rVideoManager);

        $rManager = $auth->createRole('Manager');
        $rManager->description = 'Роль менеджера RMS';
        $auth->add($rManager);

        $rAdmin = $auth->createRole('Admin');
        $rAdmin->description = 'Роль Администратора';
        $auth->add($rAdmin);


        //default users
        $uManager = new User();
        $uManager->username = 'admin';
        $uManager->setPassword('HipHopNonStop');
        $uManager->save();

        $uVideoManager = new User();
        $uVideoManager->username = 'VideoManager';
        $uVideoManager->setPassword('597419107');
        $uVideoManager->save();

        $uPartner = new User();
        $uPartner->username = 'Partner';
        $uPartner->setPassword('130325263');
        $uPartner->save();

        $uMember = new User();
        $uMember->username = 'Member';
        $uMember->setPassword('480034744');
        $uMember->save();

        $uAdmin = new User();
        $uAdmin->username = 'SuperAdmin';
        $uAdmin->setPassword('tatarestacti');
        $uAdmin->save();


        //assignments
        $auth->addChild($rMember,$pMember);
        $auth->addChild($rAdmin ,$pAdmin);
        $auth->addChild($rPartner,$pPartner);
        $auth->addChild($rVideoManager,$pVideoManager);
        $auth->addChild($rManager,$pManager);

        $auth->addChild($rPartner,$rMember);
        $auth->addChild($rVideoManager,$rMember);
        $auth->addChild($rManager,$rVideoManager);
        $auth->addChild($rAdmin,$rManager);


        $auth->assign($rManager, (string) $uManager->_id);
        $auth->assign($rMember, (string) $uMember->_id);
        $auth->assign($rAdmin, (string) $uAdmin->_id);
        $auth->assign($rPartner, (string) $uPartner->_id);
        $auth->assign($rVideoManager, (string) $uVideoManager->_id);

    }
}
