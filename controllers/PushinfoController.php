<?php

namespace app\controllers;

use app\models\Source;
use Yii;
use app\models\PushInfo;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Picmsg;
use app\models\Wechat;

/**
 * PushinfoController implements the CRUD actions for PushInfo model.
 */
class PushinfoController extends Controller
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
     * Lists all PushInfo models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => PushInfo::find()
                ->where(['is_delete' => PushInfo::IS_DELETE_FALSE])
                ->orderBy('create_time DESC'),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new PushInfo model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionEdit()
    {
        $id = Yii::$app->request->get('id', 0);
        $data = [];
        $notids = '';
        if ($id != 0) {
            $model = $this->findModel($id);
            $ids = explode(',', $model->push_detail);
            $list = Picmsg::find()
                ->where(['id' => $ids])
                ->indexBy('id')
                ->all();

            foreach ($ids as $k => $index_id) {
                $notids .= "'" . $index_id . "',";
                $data[] = $list[$index_id];
            }
        }
        $notids = trim($notids, ',');
        $where = '1=1';
        if (!empty($list)) {
            $where .= ' and picmsg.id not in (' . $notids . ')';
        }

        $expire_time = time() - PUSH_THREE_DAYS_DIFF_TIME;
        $dataProvider = new ActiveDataProvider([
            'query' => Picmsg::find()
                ->leftJoin('source', 'source.id=picmsg.source_id')
                ->where(['picmsg.status' => Picmsg::STATUS_IS_DEFAULT])
                ->andWhere($where)
                ->andWhere('source.upload_time > :expire_time', [':expire_time' => $expire_time])
                ->orderBy('picmsg.create_time DESC')
                ->limit(10),
            'pagination' => false
        ]);

        return $this->render('edit', [
            'id' => $id,
            'data' => $data,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionSave()
    {
        $id = Yii::$app->request->post('id');
        $ids = Yii::$app->request->post('ids');
        $ids = trim(trim($ids, 0), ',');

        $ret = [
            'errno' => 0,
            'error' => '操作成功'
        ];

        if ($id == 0) {
            $model = new PushInfo();
            $model->create_time = $model->update_time = time();
        } else {
            $model = PushInfo::findOne($id);
            if (empty($model)) {
                $ret['errno'] = 1;
                $ret['error'] = '参数错误';
                return json_encode($ret);
            }
            $model->update_time = time();
        }

        $array_ids = explode(',', $ids);
        $data = [];

        foreach ($array_ids as $k => $v) {
            if ($v == 0)continue;
            $picmsg = Picmsg::findOne($v);
            $data['articles'][$k]['thumb_media_id'] = Source::returnMediaId($picmsg->source_id);
            $data['articles'][$k]['title'] = $picmsg->title;
            $data['articles'][$k]['author'] = $picmsg->author;
            $data['articles'][$k]['content_source_url'] = $picmsg->source_url;
            $data['articles'][$k]['content'] = $picmsg->content;
            $data['articles'][$k]['digest'] = $picmsg->description;
            $data['articles'][$k]['show_cover_pic'] = $picmsg->show_cover_pic;
        }

        $weObj = new Wechat($this->getOptions());
        $uploadArticles = $weObj->uploadArticles($data);
        Yii::warning('upload result:' . json_encode($uploadArticles));
        if (!$uploadArticles) {
            $ret['errno'] = 1;
            $ret['error'] = '接口请求失败';
            return json_encode($ret);
        }

        if ($uploadArticles['type'] == 'news') {
            $model->type = PushInfo::TYPE_NEWS;
        }
        $model->push_detail = $ids;
        $model->media_id = $uploadArticles['media_id'];
        $model->status = PushInfo::STATUS_IS_UNPUSH;
        if (!$model->save()) {
            Yii::warning('pushinfo save fail, id:' . $id . ', error:' . json_encode($model->getErrors()));
            $ret['errno'] = 1;
            $ret['error'] = '保存失败,请稍后重试';
            return json_encode($ret);
        }

        return json_encode($ret);
    }

    public function actionPreview($id)
    {
        $ret = [
            'errno' => 0,
            'error' => '操作成功'
        ];

        $model = PushInfo::findOne($id);
        if (empty($model)) {
            $ret['errno'] = 1;
            $ret['error'] = '参数错误';
            return json_encode($ret);
        }

        $alert_msg = $this->checkPicmsgUploadTime($model);
        if (!empty($alert_msg)) {
            $ret['errno'] = 1;
            $ret['error'] = '【'. trim($alert_msg, '、') . '】，文章截图已过期，请更换缩略图后操作';
            return json_encode($ret);
        }

        $preview_array_ids = Yii::$app->params['preview_open_ids'];
        foreach ($preview_array_ids as $open_id) {
            $data = [
                'touser' => $open_id,
                'mpnews' => [
                    'media_id' => $model->media_id,
                ],
                'msgtype' => 'mpnews',
            ];

            $weObj = new Wechat($this->getOptions());
            $result = $weObj->previewMassMessage($data);
            Yii::warning('preview, open_id:' . $open_id . ', result:' . json_encode($result));
        }

        return json_encode($ret);
    }

    public function actionPush($id)
    {
        $ret = [
            'errno' => 0,
            'error' => '操作成功'
        ];

        $pushInfo = PushInfo::findOne($id);
        if (empty($pushInfo)) {
            $ret['errno'] = 1;
            $ret['error'] = '参数错误';
            return json_encode($ret);
        }

        $alert_msg = $this->checkPicmsgUploadTime($pushInfo);
        if (!empty($alert_msg)) {
            $ret['errno'] = 1;
            $ret['error'] = '【'. trim($alert_msg, '、') . '】，文章截图已过期，请更换缩略图后操作';
            return json_encode($ret);
        }

        $data = [
            "filter" => [
                "is_to_all" => true,//true推送给全部好友，“tag_id”的部份好友无效；false推送给部份已关注用户
                "tag_id" => 100,//部份已关注用户
            ],
            "mpnews" => [
                "media_id" => $pushInfo->media_id
            ],
            "msgtype" => "mpnews",
            "send_ignore_reprint" => 1
        ];

        $weObj = new Wechat($this->getOptions());
        $result = $weObj->sendGroupMassMessage($data);
//        $result = [
//            "errcode" => 0,
//            "errmsg" => "send job submission success",
//            "msg_id" => 1000000002,
//            "msg_data_id" => 2247483657
//        ];

        if (!$result) {
            $ret['errno'] = 1;
            $ret['error'] = '微信接口请求失败';
            return json_encode($ret);
        }

        if ($result['errcode'] != 0) {
            $ret['errno'] = 1;
            $ret['error'] = '发送失败';
            return json_encode($ret);
        }

        $pushInfo->status = PushInfo::STATUS_IS_PUSHED;
        $pushInfo->msg_id = $result['msg_id'];
        $pushInfo->msg_data_id = $result['msg_data_id'];
        $pushInfo->push_time = time();
        if (!$pushInfo->update()) {
            Yii::warning('push fail, id:' . $id . ', error:' . json_encode($pushInfo->getErrors()));
            $ret['errno'] = 1;
            $ret['error'] = '发送成功,系统保存失败';
            return json_encode($ret);
        }
        return json_encode($ret);
    }

    protected function checkPicmsgUploadTime($pushInfo)
    {
        $ids = explode(',', $pushInfo->push_detail);
        $picmsgs = Picmsg::find()
            ->select('picmsg.title, picmsg.source_id, source.upload_time')
            ->leftJoin('source', 'source.id=picmsg.source_id')
            ->where(['picmsg.id' => $ids])
            ->asArray()
            ->all();
        $alert_msg = '';
        $current_time = time();
        foreach ($picmsgs as $v) {
            if ($v['upload_time'] + PUSH_THREE_DAYS_DIFF_TIME < $current_time) {
                $alert_msg .= $v['title'] . '、';
            }
        }
        return $alert_msg;
    }

    /**
     * Deletes an existing PushInfo model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $ret = [
            'errno' => 0,
            'error' => '操作成功'
        ];

        $pushInfo = PushInfo::findOne($id);
        if (empty($pushInfo)) {
            $ret['errno'] = 1;
            $ret['error'] = '参数错误';
            return json_encode($ret);
        }

        if (!empty($pushInfo->msg_id)) {
            $weObj = new Wechat($this->getOptions());
            $result = $weObj->deleteMassMessage($pushInfo->msg_id);
            Yii::warning("delete message, id:$id, result:" . json_encode($result));

            if (!$result) {
                $ret['errno'] = 1;
                $ret['error'] = '微信接口请求失败';
                return json_encode($ret);
            }
        }

        $pushInfo->is_delete = PushInfo::IS_DELETE_TRUE;
        if (!$pushInfo->update()) {
            Yii::warning("delete message update fail, id:$id, error:" . json_encode($pushInfo->getErrors()));
            $ret['errno'] = 1;
            $ret['error'] = '数据更新失败';
            return json_encode($ret);
        }

        return json_encode($ret);
    }

    /**
     * Finds the PushInfo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PushInfo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PushInfo::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
