<?php

namespace app\commands;

use yii\console\Controller;

class HtestController extends Controller
{

    public $app_id = 'wx8a002dd5171a409c';
    public $app_secret = '86343a69de643f9233e817bf7af55912';

    public function actionIndex()
    {
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->app_id&secret=$this->app_secret";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        var_dump($output);
    }
}
