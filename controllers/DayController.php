<?php

namespace app\controllers;

use app\models\DayStat;
use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;

class DayController extends Controller
{
    
    public function actionIndex()
    {
        $query = DayStat::find()
            ->select('openid')
            ->where(['day' => strtotime(date('Ymd'))])
            ->groupBy('openid');

        $dayDataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->render('index', [
            'dayDataProvider' => $dayDataProvider,
        ]);
    }

}
