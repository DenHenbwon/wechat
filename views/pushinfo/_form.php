<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PushInfo */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="push-info-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'show_cover_picmsg_id')->textInput() ?>

    <?= $form->field($model, 'push_detail')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'media_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'create_time')->textInput() ?>

    <?= $form->field($model, 'update_time')->textInput() ?>

    <?= $form->field($model, 'push_time')->textInput() ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
