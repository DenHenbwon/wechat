<?php
namespace app\controllers;

use app\models\DayStat;
use app\models\Menu;
use app\models\ReKwInfo;
use app\models\SourceInfo;
use app\models\Users;
use Yii;
use yii\web\Controller;
use app\models\Wechat;
use app\models\KeyWords;

class WechatController extends Controller
{

    public $enableCsrfValidation = false;

    protected $app_id = WECHAT_APPID;
    protected $app_secret = WECHAT_APPSECRET;
    protected $token = WECHAT_TOKEN;
    protected $encoding_key = WECHAT_ENCODINGAESKEY;

    protected $arr = [
        [
            'ask' => ['提现失败', '提现不了', 1],
            'ans' => "您好\nA.提现失败 请您检查您绑定微信实名和所填写真实姓名及身份证号是否属于同一个人\nB.如果显示您的一笔提现正在进行 就是您的正在进行一笔提现操作 得等这笔提现完成才能进行新的提现"
        ],
        [
            'ask' => ['完成不了', 2],
            'ans' => "您好，任务类问题遇到打不开完成不了情况的您可以截图在钱庄app里申诉"
        ],
    ]

    public function beforeAction($action)
    {
        //记录日志到专用文件中
        Yii::$app->tool->setLogFile('wechat');
        return parent::beforeAction($action);
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
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

    public function actionIndex()
    {
        $weObj = new Wechat($this->getOptions());
        $weObj->valid();//明文或兼容模式可以在接口验证通过后注释此句，但加密模式一定不能注释，否则会验证失败
        $type = $weObj->getRev()->getRevType();
        $open_id = $weObj->getRevFrom();
        $event_arr = $weObj->getRevEvent();
        $event_key = isset($event_arr['key']) ? $event_arr['key'] : '';
        $event_type = $event_arr['event'];

        switch($type) {
            case Wechat::MSGTYPE_TEXT:
                $msg = $weObj->getRevContent();
                $ans = $this->getAnswer($msg);
                /*
                if (isset($ans['id'])) {
                    $daystat = DayStat::findOne(['openid' => $open_id, 'keyword' => $ans['id'], 'day' => strtotime(date("Ymd"))]);
                    $time = time();
                    if ($daystat) {
                        $daystat->num ++;
                        $daystat->update_time = $time;
                    } else {
                        $daystat = new DayStat();
                        $daystat->openid = $open_id;
                        $daystat->keyword = $ans['id'];
                        $daystat->day = strtotime(date("Ymd"));
                        $daystat->num = 1;
                        $daystat->create_time = $time;
                        $daystat->update_time = $time;
                    }

                    if (!$daystat->save()) {
                        Yii::warning('关键词统计失败, openid:' . $open_id . ', keyyword:' . $ans['keyword'] . 'msg:' . json_encode($daystat->errors, JSON_UNESCAPED_UNICODE));
                    }

                    $data = [
                        [
                            'Title' => $ans['title'],
                            'Description' => $ans['description'],
                            'PicUrl' => HOST_UPLOADS_PATH . $ans['imgurl'],
                            'Url' => $ans['url'],
                        ]
                    ];
                    return $weObj->news($data)->reply();
                } else {
                    if (!empty($ans['reply'])) {
                        $data1 = [
                            'touser' => $open_id,
                            'msgtype' => 'text',
                            'text' => [
                                'content' => $ans['reply'],
                            ],
                        ];
                        $weObj->sendCustomMessage($data1);
                    }
                }
                */

                if (!empty($ans['reply'])) {
                    $data1 = [
                        'touser' => $open_id,
                        'msgtype' => 'text',
                        'text' => [
                            'content' => $ans['reply'],
                        ],
                    ];
                    $weObj->sendCustomMessage($data1);
                }
                exit;
                break;


               /* if (!empty($ans['source'])) {
                    $arr = explode(',', substr($ans['source'], 0, -1));
                    foreach ($arr as $item) {
                        $source = SourceInfo::findOne(['id' => $item]);
                        if ($source) {//确认资源存在才做处理
//                            if ($source->type == SourceInfo::TYPE_IMAGE) {回复图片
//                                $data = [
//                                    'touser' => $open_id,
//                                    'msgtype' => 'image',
//                                    'image' => [
//                                        'media_id' => json_decode($source->path, true)['media_id'],
//                                    ],
//                                ];
//                                $weObj->sendCustomMessage($data);
//                            } else {
                                $data = [
                                    'touser' => $open_id,
                                    'msgtype' => 'text',
                                    'text' => [
                                        'content' => $source->path,
                                    ],
                                ];
                                $weObj->sendCustomMessage($data);
//                            }
                        }
                    }
                }
               */

            case Wechat::MSGTYPE_EVENT:
                if ($event_type == Wechat::EVENT_SUBSCRIBE) {
//                    $kws = ReKwInfo::find()
//                        ->select('keyword')
//                        ->orderBy('create_time DESC')
//                        ->all();
//                    $text = "我们等你好久啦\n
//获取更详细的信息，请回复关键词:";
//                    foreach ($kws as $k => $v) {
//                        if ($k == 0) {
//                            $text .= $v->keyword;
//                        } else {
//                            $text .= '、' . $v->keyword;
//                        }
//                    }
//                    $text .= "~\n
//更多内容请点击菜单“公司官网”了解~
//                    ";
//                    Yii::warning("open_id:$open_id follow");
                    $msg = "欢迎关注天天钱庄VIP，天天钱庄是一款用手机赚钱的强大应用，零投入无需任何本金，提现快捷方便。\n\n赚钱方法：\n1.输入“下载”，下载安装钱庄app并注册\n2.从钱庄app里接任务\n3.试玩2-3分钟获得任务奖励\n4.在余额里点击立即提现 提现到微信\n\n问题导航：输入数字编号获得相应问题的快速解答（如输入 1,提现失败）\n[1]提现失败\n[2]任务完成不了或打不开\n[3]帐号异常\n[4]解绑微信手机号\n[5]修改绑定姓名\n[6]找回帐号\n[7]钱庄任务\n[8]下载天天钱庄\n[9]安卓用户\n[10]提现进度\n[11]身份认证失败\n[12]高额任务\n\nps：强烈建议下载app后第一时间阅读App内的常见问题 最大限度减少大家上手难度！";
                    $weObj->text($msg)->reply();
                }
//                else {
//                    $data = [
//                        'touser' => $open_id,
//                        'msgtype' => 'news',
//                        'news' => [
//                            "articles" => [
//                                [
//                                    'title' => '终于等到你，还好没放弃',
//                                    //'title' => '点击“开始赚钱”，开启掌上赚钱之旅',
//                                    //'description' => '下载有条App送1元红包，下载应用领1-4元，比某宝收益强多了！',
//                                    'description' => '点击开始试玩，Get最新最酷的果粉技巧',
//                                    //'url' => 'http://appems.com/install',
//                                    'url' => 'http://app.ancloud.com.cn/install',
//                                    //'picurl' => 'http://appems.com/img/wechat_msg_head.jpg',
//                                    'picurl' => 'http://7xrk3a.com1.z0.glb.clouddn.com/welcome.jpg',
//                                ]
//                            ],
//                        ],
//                    ];
//
//                    return $weObj->sendCustomMessage($data);
//                }
                exit;
                break;
            case Wechat::MSGTYPE_IMAGE:
                break;
            default:
                $weObj->text("您好，您的反馈已收到，我们将尽快回复您:)")->reply();
        }
        exit;
    }

    public function bindInvite($open_id, $from_user)
    {
        $from_user_id = str_replace('qrscene_', '', $from_user);

        $redis = new RedisStore();
        return $redis->setInviteOpenid($open_id, $from_user_id);
    }

    /**
     * 同步公众号菜单
     */
    public function actionSyncmenu()
    {
        $weObj = new Wechat($this->getOptions());
        $newmenu = [
            "button" => Menu::getWxBtn(),
        ];

        $result = $weObj->createMenu($newmenu);
        echo '<pre>';
        var_dump($newmenu);
        var_dump($result);
        exit;
    }

    /**
     * 获取关键词回复
     * @param $msg
     *
     * @return array|null|\yii\db\ActiveRecord
     */
    public function getAnswer($msg)
    {
        /*
        $replyInfo = ReKwInfo::find()
            ->where(['like', 'keyword', $msg])
            ->orderBy('create_time DESC')
            ->asArray()
            ->one();
        if ($replyInfo) {
            return $replyInfo;
        }

        $data['source'] = '';
        $data['reply'] = '暂时未收录此关键词';
        return $data;
        */
        
        $data['source'] = '';
        $data['reply'] = '暂时未收录此关键词';
        foreach ($this->arr as $v) {
            if (in_array($msg, $v['ask'])) {
                $data['reply'] = $v['ans'];
            }
        }
        return $data;
    }

    public function actionSyncfollowusers()
    {
        $weObj = new Wechat($this->getOptions());
        $result = $weObj->getUserList();
        if (isset($result['data']['openid'])) {
            $openids = $result['data']['openid'];

            $delete_num = Users::deleteAll();
            Yii::warning("unfollow num:$delete_num");

            foreach ($openids as $openid) {
                $userInfo = $weObj->getUserInfo($openid);
                if (!$userInfo) {
                    continue;
                }

                $users = new Users();
                $users->open_id = $openid;
                $users->nick_name = $userInfo['nickname'];
                $users->remark_name = $userInfo['remark'];
                $users->sex = $userInfo['sex'];
                $users->language = $userInfo['language'];
                $users->country = $userInfo['country'];
                $users->province = $userInfo['province'];
                $users->city = $userInfo['city'];
                $users->headimgurl = $userInfo['headimgurl'];
                $users->subscribe_time = $userInfo['subscribe_time'];
                $users->groupid = $userInfo['groupid'];
                $users->tagid_list = implode(',', $userInfo['tagid_list']);

                if (!$users->save()) {
                    Yii::warning('sync users fail, userInfo:' . serialize($userInfo));
                    continue;
                }
            }
        }
    }
}
