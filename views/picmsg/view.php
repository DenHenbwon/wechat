<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Picmsg */

$this->title = '图文详情';
?>
<div class="picmsg-view">

    <div style="margin: 10px auto;">
        <span class="pull-right">
            <?= Html::a('修改', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('删除', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => '您确定要删除此项吗?',
                    'method' => 'post',
                ],
            ]) ?>
        </span>

        <ul class="nav nav-tabs">
            <li role="presentation" class="active"><a href="javascript:;"><?= Html::encode($this->title) ?></a></li>
        </ul>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'author',
            'title',
            'source_url',
            'description',
            [
                'attribute' => 'source_id',
                'label' => '缩略图',
                'format' => 'raw',
                'value' => '<img src="' . HOST_UPLOADS_PATH . \app\models\Source::getSourceInfo($model->source_id)->file_name . '" />'
            ],
            [
                'attribute' => 'content',
                'format' => 'raw',
            ],
            [
                'attribute' => 'status',
                'value' => $model->getStatusLabel()
            ],
            'create_time:datetime',
            'update_time:datetime',
            'send_time:datetime',
        ],
    ]) ?>
</div>

<style>
    .picmsg-view img{
        max-height:200px;
        max-width:300px;
    }
</style>
