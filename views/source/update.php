<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Source */

$this->title = 'Update Source: ' . $model->id;
?>
<div class="source-update">

    <div style="margin: 10px auto;">
        <ul class="nav nav-tabs">
            <li role="presentation" class="active"><a href="javascript:;"><?= Html::encode($this->title) ?></a></li>
        </ul>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
