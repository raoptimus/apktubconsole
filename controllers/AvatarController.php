<?php
namespace app\controllers;

use app\components\Transliterator;
use app\models\users\AppUser;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class AvatarController extends AccController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        return $behaviors;
    }

    /**
     * Возвращает аватарку пользователья с данным id
     * Если получилось определить тип картинки - возвращает его.
     * Если не получилось, либо если тип данных не был картинкой, отвечает application/octet-stream
     * @param string $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionGet($id = '') {
        $user = AppUser::findOne(['_id' => intval($id)]);

        if (empty($user) || !is_object($user)) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $decoded_image = base64_decode($user->data->bin);
        $f = finfo_open();
        $mime_type = finfo_buffer($f, $decoded_image, FILEINFO_MIME_TYPE);
        $allowed_types = [
            'image/gif',
            'image/png',
            'image/bmp',
            'image/tiff',
            'image/x-icon'
        ];
        if (!in_array($mime_type, $allowed_types)) {
            $mime_type = 'application/octet-stream';
        }

        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->getResponse()->getHeaders()
            ->set('Content-type:', $mime_type);

        return $decoded_image;
    }
}