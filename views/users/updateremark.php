<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Users */

$this->title = '修改备注名称';
?>
<div class="users-update">

    <div style="margin: 10px auto;">
        <ul class="nav nav-tabs">
            <li role="presentation" class="active"><a href="javascript:;"><?= Html::encode($this->title) ?></a></li>
        </ul>
    </div>

    <div class="users-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'uid')->hiddenInput()->label(false) ?>

        <?= $form->field($model, 'nick_name')->textInput(['maxlength' => true, 'disabled' => true]) ?>

        <?= $form->field($model, 'remark_name')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('修改备注名称', ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
