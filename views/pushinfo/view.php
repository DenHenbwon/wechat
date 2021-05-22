<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\PushInfo */

$this->title = $model->push_id;
$this->params['breadcrumbs'][] = ['label' => 'Push Infos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="push-info-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->push_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->push_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'push_id',
            'show_cover_picmsg_id',
            'push_detail',
            'media_id',
            'create_time:datetime',
            'update_time:datetime',
            'push_time:datetime',
            'status',
        ],
    ]) ?>

</div>
