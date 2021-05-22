<?php

namespace app\controllers;

use Yii;
use app\models\Menu;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Wechat;

/**
 * MenuController implements the CRUD actions for Menu model.
 */
class MenuController extends Controller
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

    public function beforeAction($action)
    {
        if (empty(Yii::$app->user->identity->id) || !in_array(Yii::$app->user->identity->id, [100])) {
            Yii::$app->response->redirect('/login');
            return false;
        }
        return true;
    }

    /**
     * Lists all Menu models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Menu::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Menu model.
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
     * Creates a new Menu model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Menu();
        
        if ($model->load(Yii::$app->request->post())) {
                $time = time();

                if ($model->par_btn == 0) {
                    $par_btns_count = Menu::find()
                        ->where(['par_btn' => 0])
                        ->count();
                    if ($par_btns_count >= 3) {
                        $model->addError('par_btn', '父级按钮最多设置3个');
                        return $this->render('create', [
                            'model' => $model,
                        ]);
                    }
                } else {
                    if (empty($model->url) || strpos($model->url, 'http') === false) {
                        $model->addError('url', '子按钮请正确设置url(以http或者https开头)');
                        return $this->render('create', [
                            'model' => $model,
                        ]);
                    }
                }

                $model->create_time = $time;
                $model->update_time = $time;
                if ($model->save()) {
                    return $this->redirect('index');
                }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Menu model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect('index');
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Menu model.
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
     * 同步公众号菜单
     */
    public function actionSync()
    {
        $ret = [
            'errno' => 0,
            'error' => '同步成功',
        ];

        $weObj = new Wechat($this->getOptions());
        $newmenu = [
            "button" => Menu::getWxBtn(),
        ];

//        $newmenu['button'] = [
//            [
//                'type' => 'click',
//                'name' => '绑定账户',
//                'key' => 'getAccountInfo',
//            ]
//        ];


        if (empty($newmenu['button'])) {
            $ret['errno'] = 1;
            $ret['error'] = '菜单不能为空';
            return json_encode($ret);
        }

        Yii::warning("mune:" . json_encode($newmenu));
        $result = $weObj->createMenu($newmenu);
        if (!$result) {
            $ret['errno'] = 1;
            $ret['error'] = '微信接口请求失败';
            return json_encode($ret);
        }
        return json_encode($ret);
    }

    /**
     * Finds the Menu model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Menu the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Menu::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
