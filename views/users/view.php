<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Users */

$this->title = '用户详情';
?>
<div class="users-view">

    <div style="margin: 10px auto;">
        <span class="pull-right">
            <a class="btn btn-primary save-push-info" href="updateremark?id=<?= $model->uid;?>">修改备注</a>
        </span>

        <ul class="nav nav-tabs">
            <li role="presentation" class="active"><a href="javascript:;"><?= Html::encode($this->title) ?></a></li>
        </ul>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'uid',
            'open_id',
            'nick_name',
            'remark_name',
            [
                'attribute' => 'sex',
                'value' => $model->getUserSex()
            ],
            'language',
            'country',
            'province',
            'city',

            [
                'attribute' => 'headimgurl',
                'format' => 'raw',
                'value' => "<img src='{$model->headimgurl}' />"
            ],

            'subscribe_time:datetime',
            'groupid',
            'tagid_list',
        ],
    ]) ?>

    <style>
        table img {
            max-height: 300px!important;
        }
    </style>

</div>
