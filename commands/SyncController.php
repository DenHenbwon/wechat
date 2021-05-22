<?php

namespace app\commands;

use yii\console\Controller;
use app\models\Users;
use app\models\Wechat;
use Yii;

class SyncController extends Controller
{
    protected $app_id = WECHAT_APPID;
    protected $app_secret = WECHAT_APPSECRET;
    protected $token = WECHAT_TOKEN;
    protected $encoding_key = WECHAT_ENCODINGAESKEY;

    public function beforeAction($action)
    {
        Yii::$app->tool->setLogFile('sync');
        return parent::beforeAction($action);
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
     * 同步已关注的用户方法
     */
    public function actionFollowusers()
    {
        $weObj = new Wechat($this->getOptions());
        $result = $weObj->getUserList();
        if (isset($result['data']['openid']) && $result['count'] > 0) {
            $openids = $result['data']['openid'];

            $open_ids_str = '';
            foreach ($openids as $openid) {
                $userInfo = $weObj->getUserInfo($openid);
                if (!$userInfo) {
                    continue;
                }

                $users = Users::findOne(['open_id' => $openid]);
                if (empty($users)) {
                    $users = new Users();
                }
                $users->open_id = $openid;
                $users->nick_name = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $userInfo['nickname']);
                $users->remark_name = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $userInfo['remark']);
                $users->sex = $userInfo['sex'];
                $users->language = $userInfo['language'];
                $users->country = $userInfo['country'];
                $users->province = $userInfo['province'];
                $users->city = $userInfo['city'];
                $users->headimgurl = $userInfo['headimgurl'];
                $users->subscribe_time = $userInfo['subscribe_time'];
                $users->groupid = $userInfo['groupid'];
                $users->status = Users::STATUS_FOLLOW;
                $users->tagid_list = implode(',', $userInfo['tagid_list']);
                if (!$users->save()) {
                    Yii::warning('sync users fail, userInfo:' . serialize($userInfo));
                    continue;
                }
                $open_ids_str .= '"' . $openid . '",';
            }
            $open_ids_str = trim($open_ids_str, ',');
            $unfollow = Yii::$app->db->createCommand()->update('users', ['status' => 1], 'open_id not in (' . $open_ids_str . ')')->execute();
            Yii::warning("unfollow num:$unfollow");
        } else {
            $unfollow = Users::updateAll(['status' => 1]);
            Yii::warning("unfollow num:$unfollow");
        }
    }
}
