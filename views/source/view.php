<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Source */

$this->title = '素材详情';
?>
<div class="source-view">

    <div style="margin: 10px auto;">
        <span class="pull-right">
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
            [
                'attribute' => 'file_name',
                'format' => 'raw',
                'value' => '<img src="' . HOST_UPLOADS_PATH . $model->file_name . '"/>'
            ],
            'media_id',
            'url',
            'file_size',
//            'file_type',
//            'media_type',
            'upload_time:datetime',
            'create_time:datetime',
            'update_time:datetime',
        ],
    ]) ?>

</div>
<style>
    .source-view img{
        max-height:400px;
        max-width:600px;
    }
</style>
