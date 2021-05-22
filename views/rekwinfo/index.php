<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '关键词回复';
?>
<div class="re-kw-info-index">

    <div style="margin: 10px auto;">
        <span class="pull-right">
            <?= Html::a('新增关键词', ['create'], ['class' => 'btn btn-success']) ?>
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
            'keyword',
            'title',
            'url',
            [
                'attribute' => 'imgurl',
                'format' => 'raw',
                'value' => function ($data) {
                    return '<img src="' . $data->getImgUrl() . '">';
                }
            ],
            'create_time:datetime',
            'update_time:datetime',

            [
                'class' => 'yii\grid\ActionColumn',
            ],
        ],
    ]); ?>
    <style>
        table img {
            max-height:200px !important;
        }
    </style>
</div>
