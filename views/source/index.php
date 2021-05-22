<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '素材管理';
$action = Yii::$app->controller->action->id;
?>
<div class="source-index">

    <div style="margin: 10px auto;">
        <span class="pull-right">
            <?= Html::a('上传临时素材', ['create'], ['class' => 'btn btn-success']) ?>
        </span>

        <ul class="nav nav-tabs">
            <li role="presentation" <?= $action == 'index' ? 'class="active"' : '';?>><a href="/source">可用素材</a></li>
            <li role="presentation" <?= $action == 'expire-material' ? 'class="active"' : '';?>><a href="/source/expire-material">过期素材</a></li>
        </ul>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => "{items}\n{pager}",
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'file_name',
                'format' => 'raw',
                'value' => function($data) {
                    return '<img src="' . HOST_UPLOADS_PATH . $data->file_name . '"/>';
                }
            ],
            'media_id',
            [
                'attribute' => 'url',
                'format' => 'raw',
                'value' => function($data) {
                    return empty($data->url) ? '-' : $data->url;
                }
            ],
            'file_size',
//            'file_type',
//            'media_type',
            'upload_time:datetime',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {delete}'
            ],
        ],
    ]); ?>
</div>

<style>
    .source-index img{
        max-height:200px;
        max-width:300px;
    }
</style>
