<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

$current_controller = Yii::$app->controller->id;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<style>
    .navbar-nav .navbar-right .nav {

    }
</style>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    if ($current_controller == 'login') {
        $css = <<<EOF
        body{
            background: url(/images/bg.jpg) no-repeat 0px 0px;
        }
EOF;
        $this->registerCss($css);
    } else {
        NavBar::begin([
            'brandLabel' => '公众号后台',//祥胜建材
            'brandUrl' => Yii::$app->homeUrl,
            'options' => [
                'class' => 'navbar-inverse navbar-fixed-top',
            ],
        ]);
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav navbar-right'],
            'items' => [
                ['label' => '后台管理', 'url' => 'http://www.laxsjc.com/admin'],
                ['label' => '菜单管理', 'url' => ['/menu/index']],
                ['label' => '所有用户', 'url' => ['/users/index']],
                ['label' => '关键字回复', 'url' => ['/rekwinfo/index']],
                ['label' => '素材管理', 'url' => ['/source/index']],
                ['label' => '图文素材', 'url' => ['/picmsg/index']],
                ['label' => '推送列表', 'url' => ['/pushinfo/index']],
                ['label' => '回复统计', 'url' => ['/day/index']],
                Yii::$app->user->isGuest ? (
                ['label' => 'Login', 'url' => ['/login/index']]
                ) : (
                    '<li>'
                    . Html::beginForm(['/login/logout'], 'post')
                    . Html::submitButton(
                        'Logout (' . Yii::$app->user->identity->username . ')',
                        ['class' => 'btn btn-link logout']
                    )
                    . Html::endForm()
                    . '</li>'
                )
            ],
        ]);
        NavBar::end();
    }
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= $content ?>
    </div>
</div>

<?php if ($current_controller != 'login'):?>
    <footer class="footer">
        <div class="container">
            <p class="pull-left">&copy; 吕卫杰 <?= date('Y') ?></p><!--祥胜建材-->

            <p class="pull-right" style="color:#eee;">hjl</p>
        </div>
    </footer>
<?php endif;?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
