<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="users-index">

    <div style="margin: 10px auto;">
        <ul class="nav nav-tabs">
            <li role="presentation" <?= $action == 'index' ? 'class="active"' : '';?>><a href="/users/index">已关注用户</a></li>
            <li role="presentation" <?= $action == 'unfollow' ? 'class="active"' : '';?>><a href="/users/unfollow">取消关注用户</a></li>
        </ul>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => "{items}\n{pager}",
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'headimgurl',
                'format' => 'raw',
                'value' => function($data) {
                    return "<img src='{$data->headimgurl}' height='120' />";
                }
            ],

            [
                'attribute' => 'nick_name',
                'value' => function($data) {
                    return empty($data->remark_name) ? $data->nick_name : $data->remark_name . "（{$data->nick_name}）";
                }
            ],

            'city',

            [
                'attribute' => 'sex',
                'value' => function($data) {
                    return $data->getUserSex();
                }
            ],


            // 'language',
            // 'country',
            // 'province',

            // 'headimgurl:url',
             'subscribe_time:datetime',
            // 'groupid',
            // 'tagid_list',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}'
            ],
        ],
    ]); ?>
</div>
