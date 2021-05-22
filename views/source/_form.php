<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;

/* @var $this yii\web\View */
/* @var $model app\models\Source */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="source-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'file_name')->hiddenInput(['maxlength' => true])->label(false) ?>

    <?= $form->field($model, 'source')->widget(FileInput::classname(), [
        'options' => ['multiple' => false],
    ])->label('素材');?>

    <!--?= $form->field($model, 'media_id')->textInput(['maxlength' => true]) ?-->

    <!--?= $form->field($model, 'file_type')->textInput() ?-->

    <?= $form->field($model, 'media_type')->dropDownList(\app\models\Source::getMediaTypeList(true)) ?>

    <!--?= $form->field($model, 'upload_time')->textInput() ?-->

    <!--?= $form->field($model, 'create_time')->textInput() ?-->

    <!--?= $form->field($model, 'update_time')->textInput() ?-->

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
