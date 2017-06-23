<?php

namespace app\controllers;

use app\models\video\Comment;
use app\models\video\CommentSearch;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * CommentController implements the CRUD actions for Comment model.
 */
class VideoCommentController extends AccController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access']['rules'][0]['roles'] = ['VideoManager'];
        return $behaviors;
    }

    /**
     * Lists all appUser models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $this->pageTitle = 'Comments';
        $searchModel = new CommentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionSpam($id)
    {
        $v = $this->findModel($id);

        if ($v->spam()) {
            return $this->flashResult("success", "Коммент помечен как спам", '/video-comment/index' , ['after' => "toggleActive"]);
        } else {
            return $this->flashResult('error', "Произошла ошибка", '/video-comment/index');
        }
    }

    public function actionRemove($id)
    {
        $v = $this->findModel($id);

        if ($v->remove()) {
            return $this->flashResult("success", "Коммент удалён", '/video-comment/index' , ['after' => "toggleActive"]);
        } else {
            return $this->flashResult('error', "Произошла ошибка", '/video-comment/index');
        }
    }

    /**
     * @param $id
     * @return Comment
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Comment::findOne(intval($id))) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}