<?php

namespace app\controllers;

use app\models\Wechat;
use Yii;
use app\models\Users;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UsersController implements the CRUD actions for Users model.
 */
class UsersController extends Controller
{
    protected $app_id = WECHAT_APPID;
    protected $app_secret = WECHAT_APPSECRET;
    protected $token = WECHAT_TOKEN;
    protected $encoding_key = WECHAT_ENCODINGAESKEY;

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

    protected function getOptions()
    {
        return array(
            'token'=> $this->token,
            'encodingaeskey'=> $this->encoding_key,
            'appid'=> $this->app_id,
            'appsecret'=>$this->app_secret,
        );
    }

    /**
     * Lists all Users models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Users::find()
                ->where(['status' => Users::STATUS_FOLLOW])
                ->orderBy('subscribe_time DESC'),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'action' => 'index',
        ]);
    }

    public function actionUnfollow()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Users::find()
                ->where(['status' => Users::STATUS_UNFOLLOW])
                ->orderBy('subscribe_time DESC'),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'action' => 'unfollow',
        ]);
    }

    public function actionUpdateremark($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            if (empty($model->remark_name)) {
                $model->addError('remark_name', '备注的名称不能为空');
                return $this->render('updateremark', [
                    'model' => $model,
                ]);
            }

            $weObj = new Wechat($this->getOptions());
            $result = $weObj->updateUserRemark($model->open_id, $model->remark_name);
            if (!$result) {
                $model->addError('remark_name', '微信接口请求失败，请稍后重试！');
                return $this->render('updateremark', [
                    'model' => $model,
                ]);
            }

            if ($result['errcode'] == 0 && $model->update()) {
                return $this->redirect(['view', 'id' => $model->uid]);
            }

            $model->addError('remark_name', '保存失败，请稍后重试！');
            return $this->render('updateremark', [
                'model' => $model,
            ]);
        }

        return $this->render('updateremark', [
            'model' => $model,
        ]);
    }

    /**
     * Displays a single Users model.
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
     * Updates an existing Users model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->uid]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Users model.
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
     * Finds the Users model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Users the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Users::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
