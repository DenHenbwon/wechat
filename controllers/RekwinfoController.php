<?php

namespace app\controllers;

use Yii;
use app\models\ReKwInfo;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * RekwinfoController implements the CRUD actions for ReKwInfo model.
 */
class RekwinfoController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if (empty(Yii::$app->user->identity->id) || !in_array(Yii::$app->user->identity->id, [100])) {
            Yii::$app->response->redirect('/login');
            return false;
        }
        return true;
    }

    /**
     * Lists all ReKwInfo models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => ReKwInfo::find()
                ->orderBy('create_time DESC'),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ReKwInfo model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ReKwInfo model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ReKwInfo();
        if ($model->load(Yii::$app->request->post())) {
            $file = UploadedFile::getInstance($model, 'imgfile');
            if (!isset($file)) {
                $model->addError('imgfile', '请选择图片上传');
                return $this->render('create', [
                    'model' => $model,
                ]);
            }

            $file_name = Yii::$app->tool->getRandKey() . '.' . $file->extension;
            $dir = UPLOADS_PATH . $file_name;
            $file->saveAs($dir);
            $model->imgurl = $file_name;
            $model->create_time = $model->update_time = time();
            if (!$model->save()) {
                Yii::warning('reply keyword info create fail, error:' . serialize($model->getErrors()));
                return $this->error('保存失败');
            }

            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing ReKwInfo model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) ) {
            $file = UploadedFile::getInstance($model, 'imgfile');
            if (isset($file)) {
                $file_name = Yii::$app->tool->getRandKey() . '.' . $file->extension;
                $dir = UPLOADS_PATH . $file_name;
                $file->saveAs($dir);
                $model->imgurl = $file_name;
            }
            $model->update_time = time();

            if (!$model->update()) {
                Yii::warning('reply keyword info update fail, error:' . serialize($model->getErrors()));
                return $this->error('保存失败');
            }

            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing ReKwInfo model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ReKwInfo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ReKwInfo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ReKwInfo::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
