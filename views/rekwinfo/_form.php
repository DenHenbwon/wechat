<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;

/* @var $this yii\web\View */
/* @var $model app\models\ReKwInfo */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="re-kw-info-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'keyword')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    
    <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['maxlength' => true, 'rows' => 5]) ?>

    <?php
        if (!$model->isNewRecord) {
            echo '<img src="' . $model->getImgUrl() . '">';
        }
    ?>

    <?= $form->field($model, 'imgurl')->hiddenInput(['maxlength' => true])->label(false) ?>

    <?= $form->field($model, 'imgfile')->widget(FileInput::classname(), [
        'options' => ['multiple' => false],
    ]);?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
