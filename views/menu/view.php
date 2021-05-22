<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Menu */

$this->title = '菜单详情';
?>
<div class="menu-view">

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
            'id',
            'name',
            'url:url',
            'par_btn',
            'create_time:datetime',
            'update_time:datetime',
        ],
    ]) ?>

</div>
