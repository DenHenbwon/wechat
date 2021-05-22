<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\DayStat;

$this->title = '回复统计';
?>
<div style="margin: 10px auto;">
    <ul class="nav nav-tabs">
        <li role="presentation" class="active"><a href="javascript:;"><?= Html::encode($this->title) ?></a></li>
    </ul>
</div>
<div class="menu-index">
    <?= GridView::widget([
        'dataProvider' => $dayDataProvider,
        'layout' => "{items}\n{pager}",
        'columns' => [
            [
                'attribute' => 'day',
                'value' => function() {
                    return date("Y-m-d");
                }
            ],
            'openid',
            [
                'attribute' => 'keyword',
                'value' => function ($data) {
                    return $data->getSearchByOpenid();
                }
            ],
        ],
    ]); ?>
</div>

<p>
    今日搜索的关键词:<?=DayStat::getToDaySearch()?>
</p>
