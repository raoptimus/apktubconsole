<?php
/**
 * Created by IntelliJ IDEA.
 * User: ra
 * Date: 29.07.15
 * Time: 17:42
 */

namespace app\components;

use Yii;

class UrlManager extends \yii\web\UrlManager
{
    public function createUrl($params)
    {
        $url = parent::createUrl($params);
        $path = explode('/', ltrim($url, '/'));
        $projects = Yii::$app->params['projects'];
        $default = $projects[0];
        $project = Yii::$app->params['project'];

        if ($path[0] == "debug") {
            return $url;
        }
        if (in_array($path[0], $projects)) {
            $project = array_shift($path);
        }
        if ($project != $default) {
            array_unshift($path, $project);
        }
        $url = '/' . implode('/', $path);
        return $url;
    }

    public function parseRequest($request)
    {
        $ret = parent::parseRequest($request);
        $route = explode("/", $ret[0]);

        if ($route[0] == 'debug') {
            return $ret;
        }

        $projects = Yii::$app->params['projects'];
        $project = Yii::$app->params['project'];

        if (in_array($route[0], $projects)) {
            $project = array_shift($route);
        } else {
//            Yii::$app->response->redirect("/" . $project . $request->url);
        }

        header("Project:" . $project);
        header("Route:" . implode("/", $route));
        $ret[0] = implode("/", $route);
        return $ret;
    }
}