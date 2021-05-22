<?php

namespace app\components;
use app\models\DeviceInfo;
use Yii;
use app\models\RedisStore;
use app\models\WechatSdk;

class Tool
{
    const HTTP_GET_SUCCESS = 0;
    /**
     * 获取32位随机字符串
     * @return string
     */
    public function getRandKey()
    {
        return md5( time() . rand(1000, 99999999) );
    }

    /**
     * 获取随机6位数字
     * @return int
     */
    public function getRandNum()
    {
        return rand(100000, 999999);
    }

    public function getSign($params)
    {
        //Yii::error('sign input:' . json_encode($params));
        // use SORT_STRING rule
        ksort($params, SORT_STRING);

        //Yii::error('sort sign key:' . json_encode($params));

        $tmpStr = '';
        foreach($params as $value) {
            $tmpStr .= $value;
        }

        //Yii::error('sign str:' . $tmpStr);

        $tmpStr = SIGN_API_KEY . $tmpStr . SIGN_API_SECRET;

        //Yii::error('sign api key str:' . $tmpStr);
        $tmpStr = md5( $tmpStr );

        //Yii::error('sign:' . $tmpStr);

        return $tmpStr;
    }

    /**
     * v1 签名
     * @param $params
     * @return string
     */
    public function getSignV1($params)
    {
        //Yii::error('sign input:' . json_encode($params));
        // use SORT_STRING rule
        ksort($params, SORT_STRING);

        //Yii::error('sort sign key:' . json_encode($params));

        $tmpStr = '';
        foreach($params as $k => $value) {
            $tmpStr .= $k . '=' . $value . '&';
        }

        //Yii::error('sign str:' . $tmpStr);

        $tmpStr = rtrim($tmpStr, '&') . SIGN_API_SECRET;

        //Yii::error('sign api key str:' . $tmpStr);
        $tmpStr = md5( $tmpStr );

        //Yii::error('sign:' . $tmpStr);

        return $tmpStr;
    }
    
    
    

    public function getSignStrV1($params)
    {
        //Yii::error('sign input:' . json_encode($params));
        // use SORT_STRING rule
        ksort($params, SORT_STRING);

        //Yii::error('sort sign key:' . json_encode($params));

        $tmpStr = '';
        foreach($params as $k => $value) {
            $tmpStr .= $k . '=' . $value . '&';
        }

        $tmpStr = rtrim($tmpStr, '&') . SIGN_API_SECRET;
        
        return $tmpStr;
    }


    /**
     * v2 签名
     * @param $params
     * @param $app_key
     * @param $secret_key
     * @return string
     */
    public function getSignV2($app_key, $params, $secret_key)
    {
        //Yii::error('sign input:' . json_encode($params));
        // use SORT_STRING rule
        ksort($params, SORT_STRING);

        //Yii::error('sort sign key:' . json_encode($params));

        $tmpStr = '';
        foreach($params as $k => $value) {
            $tmpStr .= $k . '=' . $value . '&';
        }

        //Yii::error('sign str:' . $tmpStr);
        $tmpStr = $app_key . rtrim($tmpStr, '&') . $secret_key;

        //Yii::error('sign api key str:' . $tmpStr);
        $tmpStr = md5( $tmpStr );

        //Yii::error('sign:' . $tmpStr);

        return $tmpStr;
    }

    public function getSignStrV2($app_key, $params, $secret_key)
    {
        //Yii::error('sign input:' . json_encode($params));
        // use SORT_STRING rule
        ksort($params, SORT_STRING);

        //Yii::error('sort sign key:' . json_encode($params));

        $tmpStr = '';
        foreach($params as $k => $value) {
            $tmpStr .= $k . '=' . $value . '&';
        }

        $tmpStr = $app_key . rtrim($tmpStr, '&') . $secret_key;

        return $tmpStr;
    }


    /**
     * 回调接口中签名
     * @param $task_id
     * @param $idfa
     * @return string
     */
    public function getCallbackSign($task_id, $idfa)
    {
        return md5(CALLBACK_SIGN_SECRET . $task_id . $idfa . CALLBACK_SIGN_SECRET);
    }

    /**
     * 金额显示
     * @param $money
     * @return string
     */
    public function moneyFormat($money)
    {
        if(empty($money)) return 0;

        if (is_int($money / 100)) {
            return number_format($money / 100, 1);
        } else {
            return rtrim(number_format($money / 100, 2), '0');
        }

    }

    /**
     * 金额显示
     * @param $money
     * @return string
     */
    public function moneyFormatYuan($money)
    {
        if(empty($money)) return 0;
        if (is_int($money / 100)) {
            return number_format($money / 100, 0, '.', '');
        } else {
            return number_format($money / 100, 2, '.', '');
        }

    }

    /**
     * 显示文件大小
     * @param $size
     * @return string
     */
    public function formatSize($size)
    {
        //兼容之前按字符串填写的格式
        if (empty($size) || stripos($size, 'M') !== false) {
            return $size;
        }

        return Yii::$app->formatter->asShortSize($size, 0);
    }

    public static function getIp()
    {
        $ip = '';
        $ip_client = getenv('HTTP_CLIENT_IP') ? getenv('HTTP_CLIENT_IP') : '';
        $ip_x = getenv('HTTP_X_FORWARDED_FOR') ? getenv('HTTP_X_FORWARDED_FOR') : '';
        if ($ip_x) {
            $addrs = explode(",", $ip_x);
            $ip_x = $addrs[sizeof($addrs)-1];
        }
        $ip_remote = getenv('REMOTE_ADDR') ? getenv('REMOTE_ADDR') : '';

        if(!empty($ip_client) && filter_var($ip_client, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE) !== false)
        {
            $ip = $ip_client;
        }

        if(empty($ip) && ! empty($ip_x) && filter_var($ip_x, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE) !== false)
        {
            $ip = $ip_x;
        }

        if(empty($ip) && ! empty($ip_remote) && filter_var($ip_remote, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE) !== false)
        {
            $ip = $ip_remote;
        }

        return $ip;
    }

    /**
     * 临时设置日志文件
     * @param $file_name
     * @return bool
     */
    public function setLogFile($file_name)
    {
        if (isset(Yii::$app->log->targets[0]->logFile)) {
            Yii::$app->log->targets[0]->logFile = LOG_PATH . $file_name . '.log';
            return true;
        }

        return false;
    }

    /**
     * @param $url
     * @return mixed
     */
    public static function httpGet($url, $timeout = 5)
    {
        $ret = [
            'errno' => self::HTTP_GET_SUCCESS,
            'error' => '',
            'data' => '',
            'http_code' => 0,
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 QianZhuangApi/2.0");
        curl_setopt($ch, CURLOPT_URL, $url);

        $data = curl_exec($ch);

        $httpStatus = curl_getinfo($ch);
        $ret['http_code'] = $httpStatus['http_code'];

        if (empty($data)) {
            $ret['errno'] = 1;
            $ret['error'] = curl_error($ch);
        } else {
            $ret['data'] = $data;
        }
        curl_close($ch);

        return $ret;
    }

    public static function httpPost($url, $data, $type = 'form', $timeout = 5)
    {
        $ret = [
            'errno' => 0,
            'error' => '',
            'data' => '',
            'http_code' => 0,
        ];

        if ($type == 'raw') {
            $post_data = http_build_query($data);
        } elseif ($type == 'json') {
            $post_data = json_encode($data);
        } elseif ($type == 'header_auth') {
            $header_auth_token = $data['header_auth_token'];
            unset($data['header_auth_token']);
            $post_data = json_encode($data);
        } else {
            $post_data = $data;
        }

        $ch = curl_init();

        if ($type == 'json') {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8', 'Content-Length: ' . strlen($post_data)));
        }

        if ($type == 'header_auth') {
            $header_auth_token_str = 'Authorization:' . $header_auth_token;
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept:application/json', $header_auth_token_str));
        }

        curl_setopt ( $ch,CURLOPT_TIMEOUT, $timeout);
        //curl_setopt ( $ch,CURLOPT_VERBOSE, 1);
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 QianZhuangApi/2.0");
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post_data );

        $data = curl_exec($ch);

        $httpStatus = curl_getinfo($ch);
        $ret['http_code'] = $httpStatus['http_code'];

        if (empty($data)) {
            $ret['errno'] = 1;
            $ret['error'] = curl_error($ch);
        } else {
            $ret['data'] = $data;
        }
        curl_close($ch);

        return $ret;
    }

    /**
     * 动态绑host的curl请求方法
     * @param $host array 需要配置的域名 array("Host: appems.com");
     * @param $data string 需要提交的数据
     * @param $url string 要提交的url
     */
    public function httpPostDynamic($host, $url, $data)
    {
        $ch = curl_init();
        curl_setopt ( $ch, CURLOPT_TIMEOUT, 5);
        //curl_setopt ( $ch, CURLOPT_VERBOSE, 1);
        curl_setopt ( $ch, CURLOPT_URL, $url);
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt ( $ch, CURLOPT_HEADER, 0);
        curl_setopt ( $ch, CURLOPT_POST, 1);
        curl_setopt ( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 QianZhuangApi/2.0");
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt ( $ch, CURLOPT_HTTPHEADER, $host);
        $result = curl_exec($ch);
        //$info = curl_getinfo($ch);
        //var_dump($info);
        curl_close($ch);
        if ($result == NULL) {
            return 0;
        }
        return $result;
    }

    /**
     * 微信发消息方法
     * @param $open_id
     * @param $title
     * @param $content
     * @param $msg_id
     * @param $user_id
     * @return mixed
     */
    public function wechatMsgTool( $open_id, $title, $content, $msg_id='', $user_id=''  )
    {
        $data = array(
            'type' => 'wetchatmsg',
            'msg_id'=> $msg_id,
            'user_id'=>$user_id,
            'data' => array(
                'touser' => $open_id,
                'template_id' => 'ocpRE7zdNLJ8ZCaflOsVt7x816NpwlyjR2AQTNKzUr0',
                "url" => "",
                "topcolor" => "#FF0000",
                "data" => array(
                    "keyword1" => array("value" => $title, "color" => "#173177"),
                    "keyword2" => array("value" => date('Y-m-d H:i:s'),"color"=>"#173177"),
                    "keyword3" => array("value" => $content, "color" => "#173177"),
                )
            )
        );
        return true;
        // $redisstore = new RedisStore();
        // return $redisstore->pushQueue( REDIS_WECHAT_LPUSH_KEY,  json_encode($data) );
    }

    /*
     * 日志方法
     */
    public function logWrite( $file, $contents )
    {
        file_put_contents( $file, '['.date("Y-m-d H:i:s").'] '.$contents.PHP_EOL, FILE_APPEND );
    }

    /**
     * 发送个人模板消息
     * @param $open_id
     * @param $title
     * @param $content
     * @return array|bool
     */
    public function sendTemplateMsg($open_id, $title, $content, $msg_id = '', $user_id = '')
    {
        $options = [
            'token'=> WECHAT_TOKEN,
            'encodingaeskey'=> WECHAT_ENCODINGAESKEY,
            'appid'=> WECHAT_APPID,
            'appsecret'=> WECHAT_APPSECRET,
        ];

        $weObj = new WechatSdk($options);

        $data = array(
            'type' => 'wetchatmsg',
            'msg_id'=> $msg_id,
            'user_id' => $user_id,
            'data' => array(
                'touser' => $open_id,
                'template_id' => 'ocpRE7zdNLJ8ZCaflOsVt7x816NpwlyjR2AQTNKzUr0',
                "url" => "",
                "topcolor" => "#FF0000",
                "data" => array(
                    "keyword1" => array("value" => $title, "color" => "#173177"),
                    "keyword2" => array("value" => date('Y-m-d H:i:s'),"color"=>"#173177"),
                    "keyword3" => array("value" => $content, "color" => "#173177"),
                )
            )
        );

        $msg = $weObj->sendTemplateMessage($data['data']);

        return $msg;
    }

    /**
     * @param $data
     * @param $key, string 分类标示
     */
    public function logMsg($data = [], $key = '')
    {
        $msg = $key;

        foreach ($data as $k => $v) {
            if (is_array($v)) continue;

            if ($msg) {
                $msg .= ", ";
            }
            $msg .= "$k:$v";
        }

        return Yii::warning($msg);
    }

    /**
     * 将数组导出csv格式，直接下载
     * @param array $arr
     */
    public function exportCsv($arr = [], $attachname = 'export-data')
    {
        header("Content-Type: text/csv");
        header("Content-Disposition: attachment;filename=$attachname.csv");
        $fp = fopen('php://output', 'w');
        foreach ($arr as $row) {
            fputcsv($fp, $row);
        }
        fclose($fp);
        exit;
    }

    /**
     * 发送批量客户端消息
     * @param $bkey
     * @param $token
     * @param $msg
     * @param int $badge
     */
    public function batchClientMsg($bkey, $token, $msg, $badge = 1)
    {
        $redis = new RedisStore();

        $job = [
            'token' => $token,
            'msg' => $msg,
            'badge' => $badge,
            'time' => time(),
        ];

        return $redis->pushQueue( RedisStore::Q_CLIENT_BATCH_MSG_KEY . $bkey, json_encode($job) );
    }

    /**
     * 发送触发客户端消息
     * @param $bkey
     * @param $token
     * @param $msg
     * @param int $badge
     */
    public function eventClientMsg($bkey, $token, $msg, $badge = 1)
    {
        $redis = new RedisStore();

        $job = [
            'token' => $token,
            'msg' => $msg,
            'badge' => $badge,
            'time' => time(),
        ];

        return $redis->pushQueue( RedisStore::Q_CLOCK_EVENT_MSG_KEY . $bkey, json_encode($job) );
    }

    /**
     * 发送触发客户端消息
     * @param Array $user_names 企业微信中的用户ID
     * @param $msg
     */
    public function qyTextMsg($user_names = [], $msg)
    {
        $redis = new RedisStore();
        $touser = implode('|', $user_names);

        $job = [
            "touser" => $touser,
            "toparty" => "",
            "totag" => "",
            "safe" => "0",			//是否为保密消息，对于news无效
            "agentid" => "19",	//应用id，系统消息通知应用
            "msgtype" => "text",  //根据信息类型，选择下面对应的信息结构体

            "text" => [
                "content" => $msg,
            ]
        ];

        return $redis->pushQueue( REDIS_WECHAT_QY_MSG, json_encode($job) );
    }

    /**
     * 验证手机号码格式
     * @param $mobile
     * @return bool
     */
    public function isMobile($mobile)
    {
        // $mobile = intval($mobile);

        if (strlen($mobile) != 11) {
            return false;
        }

        if (substr($mobile, 0, 1) != 1) {
            return false;
        }

        return true;
    }

    /**
     * 获取客户端打开的URL
     * @param $bkey, string, 客户端所属的bundleid key
     * @param $bid
     * @param $time_f
     * @param string $time_t
     * @return string
     */
    public function getClientOpenUrl($bkey, $bid, $time_f, $time_t = '')
    {
        $sign_data = ['bid' => $bid, 'f' => $time_f];
        $sign = $this->getSignNew($sign_data);

        $bkey_conf = isset(Yii::$app->params['bundle_key'][$bkey]) ? Yii::$app->params['bundle_key'][$bkey] : Yii::$app->params['bundle_key'][DEFAULT_BKEY];
        $open_url = $bkey_conf['open_url'];

        return "$open_url?bid=$bid&f=$time_f&t=$time_t&s=$sign";
    }

    public function getActiveOpenUrl($bkey)
    {
        $bconf = $this->getBkeyInfo($bkey);

        return $bconf['open_url'] . md5($bkey);
    }

    /**
     * 获得对应的bkey信息
     * @param string $bkey
     * @return mixed
     */
    public function getBkeyInfo($bkey = '')
    {
        $bkey = empty($bkey) ? LAST_BKEY : $bkey;

        return Yii::$app->params['bundle_key'][$bkey];
    }

    /**
     * 比较两个字符串（区分大小写）：
     * true 如果两个字符串相等
     * true 如果 string1 小于 string2
     * false 如果 string1 大于 string2
     * @param $string1 拿来作对比的目标字串
     * @param $string2 当前需对比的字串
     * 场景示例：$string1 版本达到1.2.2的app才支持分享功能 $string2 该用户当前app版本
     * $string1 = '1.2.2'; $string2 = '1.1.8';//对比结果为false
     * $string1 = '1.2.2'; $string2 = '1.2.2';//true
     * $string1 = '1.2.2'; $string2 = '1.3.0';//true
     * @return bool
     */
    public function compareString($string1, $string2)
    {
        return strcmp($string1, $string2) <= '0' ? true : false;
    }

    /**
     * @param $seconds
     * @return string
     */
    public function timetolong($seconds)
    {
        if ($seconds > 3600){
            $hours = intval($seconds/3600);
            $minutes = $seconds % 3600;
            $time = $hours.":".gmstrftime('%M:%S', $minutes);
        }else{
            $time = gmstrftime('%M:%S', $seconds);
        }
        return $time;
    }
    public static function getCityByIp()
    {
        $ip = self::getIp();
        $ip = '36.10.18.1';
        if (empty($ip) && $ip == '127.0.0.1') {
            return false;
        }

        $api_url = 'http://m.tool.chinaz.com/ipsel?IP=' . $ip;

        $get = self::httpGet($api_url);
        $data = $get['data'];
        if ($get['http_code'] !== 200 || empty($data)) return false;

        $pattern = '/fontcolor02\">(.*?)<\/b>/';
        preg_match_all($pattern, $data, $match);
        $city_str = isset($match[1][2]) ? $match[1][2] : '';

        return $city_str ? $city_str : '';
        
    }

    public static function getRandUdid($os_type)
    {
        if ($os_type == DeviceInfo::OS_TYPE_ANDROID) {
            return mt_rand(356232021308996, 866232021308996);
        } else {
            $md1 = strtoupper(md5(mt_rand(10000000,99999999)));
            $md2 = strtoupper(md5(mt_rand(10000000,99999999)));
            $md3 = strtoupper(md5(mt_rand(10000000,99999999)));
            $md4 = strtoupper(md5(mt_rand(10000000,99999999)));
            $md5 = strtoupper(md5(mt_rand(10000000,99999999)));

            return substr($md1, 0 , 8) . '-' . substr($md2, 0 , 4) . '-' . substr($md3, 0 , 4) . '-' . substr($md4, 0 , 4) . '-' .  substr($md5, 0 , 12);
        }

    }

    /**
     * 按概率给出结果 hjl
     * @param $proArr 配置数组  [key => probability]
     * @return int|string 返回配置数组的键
     */
    public static function get_rand($proArr) {
        $result = '';
        $proSum = array_sum($proArr);
        foreach ($proArr as $key => $proCur) {
            $randNum = mt_rand(1, $proSum);             //抽取随机数
            if ($randNum <= $proCur) {
                $result = $key;                         //得出结果
                break;
            } else {
                $proSum -= $proCur;
            }
        }
        unset ($proArr);
        return $result;
    }
}
