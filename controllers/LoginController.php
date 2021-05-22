<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\LoginForm;
use yii\web\Cookie;

class LoginController extends Controller
{
    
    public function actionIndex()
    {
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            Yii::warning("admin_login, user:" . $model->username);
            return $this->redirect('/');
        }
        return $this->render('index', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->redirect('/login');
    }
}
