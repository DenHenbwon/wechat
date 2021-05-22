<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '图文素材';
?>
<div class="picmsg-index">

    <div style="margin: 10px auto;">
        <span class="pull-right">
            <?= Html::a('新增图文素材', ['create'], ['class' => 'btn btn-success']) ?>
        </span>

        <ul class="nav nav-tabs">
            <li role="presentation" class="active"><a href="javascript:;"><?= Html::encode($this->title) ?></a></li>
        </ul>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => "{items}\n{pager}",
        'columns' => [
            'id',
            [
                'attribute' => 'source_id',
                'label' => '缩略图',
                'format' => 'raw',
                'value' => function($data) {
                    return '<img src="' . HOST_UPLOADS_PATH . \app\models\Source::getSourceInfo($data->source_id)->file_name . '" />';
                }
            ],
            'author',
            'title',
            'source_url',
//            'description',

            [
                'attribute' => 'source_id',
                'label' => '截图是否过期',
                'format' => 'raw',
                'value' => function($data) {
                    return $data->judgeSourceIsExpire() ? '-' : '<font color="red">截图已过期</font>';
                }
            ],


            // 'content',
            // 'status',
            // 'create_time:datetime',
            // 'update_time:datetime',
            // 'send_time:datetime',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>

<style>
    .picmsg-index img{
        max-height:100px;
        max-width:200px;
    }
</style>
