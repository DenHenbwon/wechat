<?php

namespace app\controllers;

use app\models\Source;
use app\models\Users;
use app\models\Wechat;
use Yii;
use app\models\Picmsg;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PicmsgController implements the CRUD actions for Picmsg model.
 */
class PicmsgController extends Controller
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
     * Lists all Picmsg models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Picmsg::find()
                ->where(['is_delete' => Picmsg::IS_DELETE_FALSE])
                ->orderBy('create_time desc'),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }
    
    /**
     * Displays a single Picmsg model.
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
     * Creates a new Picmsg model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Picmsg();

        if ($model->load(Yii::$app->request->post())) {

            $model->status = Picmsg::STATUS_IS_DEFAULT;
            $model->update_time = $model->create_time = time();
            if (!$model->save()) {
                Yii::warning("create picmsg fail, error:" . json_encode($model->getErrors()));
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
     * @return string
     */
    public function actionGetThumbList()
    {
        $ret = [
            'errno' => 0,
            'error' => 'success',
            'data' => []
        ];

        $current_time = time();
        $ret['data'] = Source::find()
            ->select('id, file_name, media_id, upload_time')
            ->where('media_type=:media_type and upload_time > :upload_time', [':media_type' => Source::MEDIA_TYPE_TEMP, ':upload_time' => ($current_time - THREE_DAYS_TIME_STAMP + 1500)])
            ->asArray()
            ->all();

        if (empty($ret['data'])) {
            $ret['errno'] = 1;
            $ret['error'] = '暂无素材';
        }

        return json_encode($ret);
    }


    /**
     * Updates an existing Picmsg model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            $model->update_time = time();
            if (!$model->save()) {
               Yii::warning("update picmsg fail, error:" . json_encode($model->getErrors()));
                return $this->error('保存失败!');
            }

            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Picmsg model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $model->is_delete = Picmsg::IS_DELETE_TRUE;
        if (!$model->update()) {
            Yii::warning("delete picmsg fail, id:$id, error:" . json_encode($model->getErrors()));
            return $this->error('数据库保存失败');
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Picmsg model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Picmsg the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Picmsg::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
