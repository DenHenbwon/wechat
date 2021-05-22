<?php

namespace app\controllers;

use app\models\Wechat;
use Yii;
use app\models\Source;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * SourceController implements the CRUD actions for Source model.
 */
class SourceController extends Controller
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
     * Lists all Source models.
     * @return mixed
     */
    public function actionIndex()
    {
        $expire_time = time() - THREE_DAYS_TIME_STAMP;
        $dataProvider = new ActiveDataProvider([
            'query' => Source::find()
                ->where('upload_time > :upload_time and media_type=:media_type', [':upload_time' => $expire_time, ':media_type' => Source::MEDIA_TYPE_TEMP])
                ->orWhere(['media_type' => [Source::MEDIA_TYPE_PUSHINFO_MATERIAL, Source::MEDIA_TYPE_FOREVER]])
                ->orderBy('update_time DESC'),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionExpireMaterial()
    {
        $expire_time = time() - THREE_DAYS_TIME_STAMP;
        $dataProvider = new ActiveDataProvider([
            'query' => Source::find()
                ->where(['media_type' => Source::MEDIA_TYPE_TEMP])
                ->andWhere(['<=', 'upload_time', $expire_time])
                ->orderBy('update_time DESC'),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Source model.
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
     * Creates a new Source model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Source();
        if ($model->load(Yii::$app->request->post())) {
            $file = UploadedFile::getInstance($model, 'source');

            if ($model->media_type == Source::MEDIA_TYPE_TEMP) {
                if (!isset($file)) {
                    $model->addError('source', '请选择图片上传');
                    return $this->render('create', [
                        'model' => $model,
                    ]);
                }

                if ($file->size > 64000) {
                    $model->addError('source', '图片不能超进64KB');
                    return $this->render('create', [
                        'model' => $model,
                    ]);
                }

                if ($file->extension != 'jpg') {
                    $model->addError('source', '格式错误，图片格式只允许jpg');
                    return $this->render('create', [
                        'model' => $model,
                    ]);
                }
            }

            $file_name = Yii::$app->tool->getRandKey() . '.' . $file->extension;
            $dir = UPLOADS_PATH . $file_name;
            $file->saveAs($dir);

            $weObj = new Wechat($this->getOptions());
            $source_data['media'] = new \CURLFile($dir);
            if ($model->media_type == Source::MEDIA_TYPE_FOREVER) {
                $upload_result = $weObj->uploadForeverMedia($source_data, 'thumb');
                if (!$upload_result) {
                    $model->addError('source', '微信接口请求失败');
                    return $this->render('create', [
                        'model' => $model,
                    ]);
                }
                $model->url = $upload_result['url'];
                $model->media_id = $upload_result['thumb_media_id'];
            } elseif ($model->media_type == Source::MEDIA_TYPE_PUSHINFO_MATERIAL) {
                $upload_result = $weObj->uploadImg($source_data);
                if (!$upload_result) {
                    $model->addError('source', '微信接口请求失败');
                    return $this->render('create', [
                        'model' => $model,
                    ]);
                }
                $model->url = $upload_result['url'];
            } else {
                $upload_result = $weObj->uploadMedia($source_data, 'thumb');
                if (!$upload_result) {
                    $model->addError('source', '微信接口请求失败');
                    return $this->render('create', [
                        'model' => $model,
                    ]);
                }
                $model->media_id = $upload_result['thumb_media_id'];
            }

            Yii::warning(json_encode($upload_result));


            $model->file_size = $file->size;
            $model->upload_time = isset($upload_result['created_at']) ? $upload_result['created_at'] : time();;

            $model->file_name = $file_name;
            $model->file_type = $model->distinguishFileType($file->extension);
            $model->create_time = $model->update_time = time();
            if (!$model->save()) {
                Yii::warning('source create fail, error:' . serialize($model->getErrors()));
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
     * Updates an existing Source model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Source model.
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
     * Finds the Source model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Source the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Source::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
