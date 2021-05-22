<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Picmsg */

$this->title = '新增图文素材';
?>
<div class="picmsg-create">

    <div style="margin: 10px auto;">
        <ul class="nav nav-tabs">
            <li role="presentation" class="active"><a href="javascript:;"><?= Html::encode($this->title) ?></a></li>
        </ul>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
