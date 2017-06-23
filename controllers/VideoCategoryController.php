<?php

namespace app\controllers;

use app\models\video\Category;
use app\models\video\CategorySearch;
use Yii;
use yii\web\NotFoundHttpException;

class VideoCategoryController extends AccController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['access']['rules'][0]['roles'] = ['VideoManager'];
        return $behaviors;
    }

    /**
     * @param array $form
     * @return mixed
     */
    public function actionIndex(array $form = null)
    {
        $model = new CategorySearch();
        $model->setScenario("search");
        $model->setAttributes($form);

        $this->pageTitle = 'Category list';

        return $this->render('index', [
            'model' => $model,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);

        $this->pageTitle = $model->getLangAttr('Title');
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function actionCreate()
    {
        $this->pageTitle = 'Create category';

        $model = new Category();
        $model->setScenario("create");

        return $this->editModel($model);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $this->pageTitle = 'Update category ' . $model->getLangAttr('Title');
        $model->setScenario("update");
        return $this->editModel($model);
    }

    private function editModel(Category $model)
    {
        $model->load(Yii::$app->request->post());

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->session->setFlash("success", 'Категория успешно обновлена');
            return $this->redirect(['update', 'id' => (string) $model->_id]);
        } else {
            return $this->render('edit', [
                'model' => $model,
            ]);
        }
    }

    /**
     * @param int $id
     * @return Category
     * @throws NotFoundHttpException
     */
    private function findModel($id)
    {
        $cat = Category::findOne(intval($id));

        if (!$cat) {
            throw new NotFoundHttpException("Category not found");
        }
        return $cat;
    }
}
