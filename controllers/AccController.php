<?php
/**
 * Created by IntelliJ IDEA.
 * User: ra
 * Date: 09.05.15
 * Time: 0:44
 */

namespace app\controllers;

use app\components\CustomAccessRules;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;

class AccController extends Controller
{
    const FLASH_TYPE_INFO = "info";
    const FLASH_TYPE_SUCCESS = "success";
    const FLASH_TYPE_WARN = "warning";
    const FLASH_TYPE_ERROR = "danger";

    public $pageTitle;
    public $breadcrumbs = [];
    public $allowList = [];

    public function init()
    {
        parent::init();

        $this->breadcrumbs[] = [
            'label' => Yii::t('yii', 'Home'),
            'url' => Yii::$app->homeUrl,
        ];
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            $user = Yii::$app->getUser();
                            $identity = $user->getIdentity();

                            if ($identity->Blocked) {
                                $user->logout();
                                return false;
                            }

                            if ($user->can("beVideoManager") && !$user->can("beManager")) {
                                if (!$user->can(Yii::$app->params['viewRole'])) {
                                    return false;
                                }
                            }

                            if ($user->can("beManager")) {
                                if (!in_array(Yii::$app->request->userIP, Yii::$app->params['whiteList'])) {
                                    return false;
                                }
                            }
                            return true;
                        },

                    ],

                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Sets a flash message.
     * Return response as json for ajax request
     * Return response as redirect for !ajax request
     *
     * @param self ::FLASH_TYPE_INFO|self::FLASH_TYPE_SUCCESS|self::FLASH_TYPE_WARN|self::FLASH_TYPE_ERROR $type
     * @param string $message
     * @param null|string $returnUrl
     * @param array $jsonOptions
     *
     * @return array|string|Response
     */
    public function flashResult($type, $message, $returnUrl = null, $jsonOptions = [])
    {
        if (\Yii::$app->request->isAjax) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            return ["type" => $type, "message" => $message, "options" => $jsonOptions];
        } else {
            \Yii::$app->session->setFlash($type, $message);
        }

        if (!empty($returnUrl)) {
            return $this->redirect($returnUrl, 302);
        }
        return "";
    }
}