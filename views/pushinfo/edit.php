<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\PushInfo */

$this->title = '新增推送';

$js = <<<JS
$('table').delegate('.to-left', 'click', function(){
    var Obj = $(this);
    var parentNode = Obj.parents('tr');
    var id = Obj.attr('data-id');
    var src = Obj.attr('data-src');
    var title = Obj.attr('data-title');
    var coverId = $('.cover').attr('data-id');
    if (coverId == 0) {
        $('.cover').find('img').attr('src', src);
        $('.cover .desc').empty().text(title);
        $('.cover').attr('data-id', id);
        Obj.parents('tr').remove();
    } else {
        var notCoverLen = $('.push-list ul').children('li').length;
        if (notCoverLen == 1) {
            var notCoverId = $('.push-list ul li.parentNode').attr('data-id');
            if (notCoverId == 0) {
                $('.push-list .parentNode').find('img').attr('src', src);
                $('.push-list .parentNode .desc').empty().text(title);
                $('.push-list .parentNode').attr('data-id', id);
            } else {
                var html = '<li class="parentNode" data-id="'+id+'"><div class="thumb"><img src="' + src + '"/></div><div class="desc">' + title + '</div><div class="shadow"><a class="remove" href="javascript:;"><span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span></a></div></li>';
                $('.push-list ul').append(html);
            }
            Obj.parents('tr').remove();
        } else if (notCoverLen >= 4) {
            layer.msg('推送文章不能超过5条', {time: 3000, icon:5});
        } else {
            var html = '<li class="parentNode" data-id="'+id+'"><div class="thumb"><img src="' + src + '"/></div><div class="desc">' + title + '</div><div class="shadow"><a class="remove" href="javascript:;"><span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span></a></div></li>';
            $('.push-list ul').append(html);
            Obj.parents('tr').remove();
        }
    }
});

$('div').delegate('.remove', 'click', function(event){
    event.stopPropagation();
    var selfObj = $(this);
    var myParent = selfObj.parents('.parentNode');
    var id = myParent.attr('data-id');
    var src = myParent.find('img').attr('src');
    var title = myParent.find('.desc').text();
    
    var id = myParent.attr('data-id');
    if (id > 0) {
        var html = '<tr data-key="'+id+'"><td><a class="btn btn-default to-left" href="javascript:;" data-id="'+id+'" data-src="'+src+'" data-title="'+title+'" role="button"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span></a></td><td><img src="'+src+'"></td><td>'+title+'</td></tr>';
        $('.table tbody').append(html);
    }
    
    if (myParent.get(0).tagName == 'LI') {
        var childrenLength = myParent.parent().children('li').length;
        if (childrenLength == 1) {
            myParent.attr('data-id', 0);
            myParent.find('img').attr('src', '/images/timg.jpeg');
            myParent.find('.desc').text('非封面示例标题');
        } else {
            myParent.remove();
        }
    } else if (myParent.get(0).tagName == 'DIV') {
        myParent.attr('data-id', 0);
        myParent.find('img').attr('src', '/images/timg.jpeg');
        myParent.find('.desc').text('封面示例标题');
    }
});

$('.save-push-info').click(function() {
    var id = $('.id').attr('value');
    var coverId = $('.cover').attr('data-id');
    if (coverId == 0) {
        layer.msg('封面文章未选择', {time: 3000, icon:5});
    }
    
    var ids = coverId;
    $(".push-list ul li").each(function(){
        ids += ',' + $(this).attr('data-id');
    });
    
    $.post('/pushinfo/save', {'id':id,'ids':ids}, function(result) {
        if (result.errno == 0) {
            window.location.href="/pushinfo/index";
        } else {
            layer.msg(result.error, {time: 3000, icon:5});
            window.location.href="/pushinfo/index";
        }
    }, 'json');
});

JS;
$this->registerJs($js);

?>
<div class="push-info-create">
    <div style="margin: 10px auto;">
        <span class="pull-right">
            <a class="btn btn-primary save-push-info" href="javascript:;">保存</a>
        </span>

        <ul class="nav nav-tabs">
            <li role="presentation" class="active"><a href="javascript:;"><?= Html::encode($this->title) ?></a></li>
        </ul>
    </div>

    <div class="push-index">
        <div class="phone">
            <div class="blank">
                <input type="hidden" class="id" value="<?= $id;?>">
                <?php if (empty($data)):?>
                    <div class="cover parentNode" data-id="0">
                        <img src="/images/timg.jpeg"/>
                        <div class="desc">封面示例标题</div>
                        <div class="shadow">
                            <a class="remove" href="javascript:;"><span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span></a>
                        </div>
                    </div>

                    <div class="push-list">
                        <ul>
                            <li class="parentNode" data-id="0">
                                <div class="thumb"><img src="/images/timg.jpeg"/></div>
                                <div class="desc">非封面示例标题</div>
                                <div class="shadow">
                                    <a class="remove" href="javascript:;"><span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span></a>
                                </div>
                            </li>
                        </ul>
                    </div>

                <?php else:?>

                    <div class="cover parentNode" data-id="<?= $data[0]->id;?>">
                        <img src="<?= HOST_UPLOADS_PATH . \app\models\Source::getSourceInfo($data[0]->source_id)->file_name?>"/>
                        <div class="desc"><?= $data[0]->title;?></div>
                        <div class="shadow">
                            <a class="remove" href="javascript:;"><span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span></a>
                        </div>
                    </div>

                    <div class="push-list">
                        <ul>
                        <?php if (count($data) > 1) :?>
                            <?php foreach ($data as $k => $v):?>
                                <?php if($k==0)continue;?>
                                <li class="parentNode" data-id="<?= $v->id;?>">
                                    <div class="thumb"><img src="<?= HOST_UPLOADS_PATH . \app\models\Source::getSourceInfo($v->source_id)->file_name?>"/></div>
                                    <div class="desc"><?= $v->title?></div>
                                    <div class="shadow">
                                        <a class="remove" href="javascript:;"><span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span></a>
                                    </div>
                                </li>
                            <?php endforeach;?>

                        <?php else:?>
                            <li class="parentNode" data-id="0">
                                <div class="thumb"><img src="/images/timg.jpeg"/></div>
                                <div class="desc">非封面示例标题</div>
                                <div class="shadow">
                                    <a class="remove" href="javascript:;"><span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span></a>
                                </div>
                            </li>
                        <?php endif;?>

                        </ul>
                    </div>

                <?php endif;?>
            </div>
        </div>

        <div class="picmsg-list">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class' => 'table table-striped middle-td'],
                'layout' => "{items}\n{pager}",
                'columns' => [
                    [
                        'label' => '#',
                        'format' => 'raw',
                        'value' => function($data){
                            return '<a class="btn btn-default to-left" href="javascript:;" data-id="' . $data->id . '" data-src="' . HOST_UPLOADS_PATH . \app\models\Source::getSourceInfo($data->source_id)->file_name . '" data-title="' . $data->title . '" role="button"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span></a>';
                        }
                    ],

                    [
                        'attribute' => 'source_id',
                        'label' => '缩略图',
                        'format' => 'raw',
                        'value' => function($data) {
                            return '<img src="' . HOST_UPLOADS_PATH . \app\models\Source::getSourceInfo($data->source_id)->file_name . '" />';
                        }
                    ],
                    'title',
                ],
            ]); ?>
        </div>
    </div>
</div>
