<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\ReKwInfo */

$this->title = '回复详情';
?>
<div class="re-kw-info-view">

    <div style="margin: 10px auto;">
        <span class="pull-right">
            <?= Html::a('编辑', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
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
            'keyword',
            'title',
            'url',
            'description',
            [
                'attribute' => 'imgurl',
                'format' => 'raw',
                'value' => '<img src="' . $model->getImgUrl() . '">'
            ],
            'create_time:datetime',
            'update_time:datetime',
        ],
    ]) ?>
    <style>
        table img {
            max-height:200px !important;
        }
    </style>
</div>
