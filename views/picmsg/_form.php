<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model app\models\Picmsg */
/* @var $form yii\widgets\ActiveForm */

$host_uploads_path = HOST_UPLOADS_PATH;
$cndJsFile = '//unpkg.com/wangeditor/release/wangEditor.min.js';
$this->registerJsFile($cndJsFile);
$Js = <<<JS
    var E = window.wangEditor
        var editor = new E('#editor')
        var content = $('#picmsg-content')
        editor.customConfig.onchange = function (html) {
            content.val(html)
        }
        editor.customConfig.menus = [
            'head',  // 标题
            'bold',  // 粗体
            'italic',  // 斜体
            'underline',  // 下划线
            'strikeThrough',  // 删除线
            'foreColor',  // 文字颜色
            'backColor',  // 背景颜色
            'link',  // 插入链接
            'list',  // 列表
            'justify',  // 对齐方式
            'quote',  // 引用
            'emoticon',  // 表情
            'image',  // 插入图片
            'table',  // 表格
            //'video',  // 插入视频
            'code',  // 插入代码
            'undo',  // 撤销
            'redo'  // 重复
        ];
        editor.create();
        content.val(editor.txt.html());
     
    $('.select-thumb-submit').click(function(){
        $.ajax({
            url:'/picmsg/get-thumb-list',
            type:'get',
            data:'',
            dataType:'json',
            success:function (result) {
                if (result.errno == 0) {
                    var html = '';
                    for(var i=0; i<result.data.length; i++) {
                        html += '<li class="thumb-detail" data-path="' + result.data[i]['file_name'] + '" data-id="' + result.data[i]['id'] + '"><img src="{$host_uploads_path}' + result.data[i]['file_name'] + '"/></li>';
                    }
                    $('.modal-body').empty().append("<p class='msg' style='color:red; padding-top: 10px; display: block'></p>");
                    $('#thumb-list .msg').before(html);
                } else {
                    $('#thumb-list .msg').text(result.error);
                }
            }
        });
        
        $('#thumb-list').modal('show');
    });
    
    $('div.modal-body').delegate('li.thumb-detail', 'click', function(){
        var obj = $(this);
        var sourceId = obj.attr('data-id');
        var imgPath = "{$host_uploads_path}" + obj.attr('data-path');
        $('.field-picmsg-thumb').find('img').attr('src', imgPath);
        $('#picmsg-source_id').val(sourceId);
        $('#thumb-list').modal('toggle');
    });
JS;
$this->registerJs($Js);

Modal::begin([
    'id' => 'thumb-list',
    'header' => '请选择图文素材文章的缩略图',
]);
echo "<p class='msg' style='color:red; padding-top: 10px; display: block'></p>";
Modal::end();
?>

<div class="picmsg-form">

    <div class="form-group field-picmsg-thumb">
        <label class="control-label" for="picmsg-thumb">缩略图</label>
        <div>
            <?php
            if (isset($model->source_id) && $model->source_id > 0) {
                $sourceInfo  = app\models\Source::getSourceInfo($model->source_id);
                $path = HOST_IMG_PATH . 'timg.jpeg';
                if ($sourceInfo) {
                    $path = HOST_UPLOADS_PATH . $sourceInfo->file_name;
                }
                echo '<img src="' . $path . '" height="200"/>';
            } else {
                echo '<img src="' . HOST_IMG_PATH . 'timg.jpeg' . '" height="200"/>';
            }
            ?>
            <a href="javascript:;" class="select-thumb-submit">选取缩略图</a>
        </div>
        <div class="help-block" style="clear: both;"></div>
    </div>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'author')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'source_url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'source_id')->hiddenInput()->label(false); ?>

    <?= $form->field($model, 'show_cover_pic')->radioList(\app\models\Picmsg::getShowCoverPicList()); ?>

    <?= $form->field($model, 'description')->textarea(['maxlength' => true, 'rows' => 4]) ?>

    <div id="editor">
        <p><?= $model->content;?></p>
    </div>

    <?= $form->field($model, 'content')->hiddenInput(['maxlength' => true])->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '创建' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<style>
    .thumb {
        margin:10px auto;
    }
    #thumb-list {
        z-index: 10002;
    }

    #thumb-list li {
        padding: 5px;
        float: left;
        list-style: none;
    }

    .modal-body .msg{
        clear: both;
    }

    .thumb-detail {
        height: 100px;
        cursor: pointer;
    }

    #thumb-list .thumb-detail img {
        max-width: 100px;
        max-height: 100px;
    }
</style>
