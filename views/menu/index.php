<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '菜单管理';
$js = <<<JS
    $('.btn-sync').click(function (){
        $.get('/menu/sync', {}, function(result) {
            if (result.errno == 0) {
                layer.msg(result.error, {time: 3000, icon:6});
            } else {
                layer.msg(result.error, {time: 3000, icon:5});
            }
        }, 'json');
    });
JS;
$this->registerJs($js);

?>
<div class="menu-index">

    <div style="margin: 10px auto;">
        <span class="pull-right">
            <?= Html::a('创建按钮', ['create'], ['class' => 'btn btn-success']) ?>
            <a class="btn btn-warning btn-sync" href="javascript:;">正式更新微信按钮</a>
        </span>

        <ul class="nav nav-tabs">
            <li role="presentation" class="active"><a href="javascript:;"><?= Html::encode($this->title) ?></a></li>
        </ul>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => "{items}\n{pager}",
        'columns' => [
            'name',
            'url:url',
            [
                'attribute' => 'par_btn',
                'format' => 'raw',
                'value' => function ($data) {
                    return \app\models\Menu::getBtnName($data->par_btn);
                }
            ],
            'create_time:datetime',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
