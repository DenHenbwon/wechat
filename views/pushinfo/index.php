<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$js = <<<JS
    $('.action-preview').click(function() {
        var id = $(this).attr('data-pid');
        $.get('/pushinfo/preview?id='+id, {}, function(result) {
            if (result.errno == 0) {
                layer.msg(result.error, {time: 3000, icon:6});
                setTimeout(function(){
                    window.location.reload();
                }, 3000);
            } else {
                layer.msg(result.error, {time: 3000, icon:5});
            }
        }, 'json');
    });
    
    $('.action-push').click(function() {
        var id = $(this).attr('data-pid');
        $.get('/pushinfo/push?id='+id, {}, function(result) {
            if (result.errno == 0) {
                layer.msg(result.error, {time: 3000, icon:6});
                setTimeout(function(){
                    window.location.reload();
                }, 3000);
            } else {
                layer.msg(result.error, {time: 3000, icon:5});
            }
        }, 'json');
    });
    
    $('.action-delete').click(function() {
        var id = $(this).attr('data-pid');
        $.post('/pushinfo/delete?id='+id, {}, function(result) {
            if (result.errno == 0) {
                layer.msg(result.error, {time: 3000, icon:6});
                setTimeout(function(){
                    window.location.reload();
                }, 3000);
            } else {
                layer.msg(result.error, {time: 3000, icon:5});
            }
        }, 'json');
    });
JS;
$this->registerJs($js);

$this->title = '推送列表';
?>
<div class="push-info-index">

    <div style="margin: 10px auto;">
        <span class="pull-right">
            <?= Html::a('新增推送', ['edit'], ['class' => 'btn btn-success']) ?>
        </span>

        <ul class="nav nav-tabs">
            <li role="presentation" class="active"><a href="javascript:;"><?= Html::encode($this->title) ?></a></li>
        </ul>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'push_id',
//            'show_cover_picmsg_id',
            'push_detail',
//            'media_id',
            'create_time:datetime',
            // 'update_time:datetime',
            // 'push_time:datetime',
            [
                'attribute' => 'status',
                'value' => function($data) {
                    return $data->getPushStatusLabel();
                }
            ],
            [
                'label' => '操作',
                'format' => 'raw',
                'attribute' => 'push_id',
                'value' => function($data) {
                    $html = '';
                    if ($data->status == \app\models\PushInfo::STATUS_IS_UNPUSH) {
                        $html .= '<a href="javascript:;" class="action-preview" data-pid="'.$data->push_id.'"><span class="glyphicon glyphicon-eye-open"></span> 预览</a>';
                        $html .= '&nbsp;&nbsp;&nbsp; <a href="javascript:;" class="action-push" data-pid="'.$data->push_id.'"><span class="glyphicon glyphicon-send"></span> 推送</a>';
                        $html .= '&nbsp;&nbsp;&nbsp; <a href="/pushinfo/edit?id='.$data->push_id.'"><span class="glyphicon glyphicon-edit"></span> 编辑</a>';
                        $html .= '&nbsp;&nbsp;&nbsp; <a href="javascript:;" class="action-delete" data-pid="'.$data->push_id.'"><span class="glyphicon glyphicon-remove-sign"></span> 删除</a>';
                    }

                    if ($data->status == \app\models\PushInfo::STATUS_IS_PUSHED) {
                        $html .= '<a href="javascript:;" class="action-delete" data-pid="'.$data->push_id.'"><span class="glyphicon glyphicon-remove-sign"></span> 删除</a>';
                    }
                    return $html;
                }
            ],
        ],
    ]); ?>
</div>
