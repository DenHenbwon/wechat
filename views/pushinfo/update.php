<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PushInfo */

$this->title = 'Update Push Info: ' . $model->push_id;
$this->params['breadcrumbs'][] = ['label' => 'Push Infos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->push_id, 'url' => ['view', 'id' => $model->push_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="push-info-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
