<?php

namespace app\models;

use Yii;
use app\models\KeyWords;
/**
 * This is the model class for table "day_stat".
 *
 * @property integer $id
 * @property integer $day
 * @property string $keyword
 * @property string $openid
 * @property integer $num
 * @property integer $create_time
 * @property integer $update_time
 */
class DayStat extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'day_stat';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['day', 'num', 'create_time', 'update_time'], 'integer'],
            [['keyword'], 'string', 'max' => 128],
            [['openid'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'day' => '时间',
            'openid' => 'Opne Id',
            'keyword' => '关键词',
            'num' => '搜索次数',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }

    public function getSearchByOpenid()
    {
        $info = self::find()
            ->where(['day' => strtotime(date('Ymd'))])
            ->andWhere(['openid' => $this->openid])
            ->asArray()
            ->all();

        $str = '';
        $arr = [];
        foreach ($info as $item) {
            $arr[$item['keyword']]['keyword'] = KeyWords::getKeyWordById($item['keyword']);
            if (isset($arr[$item['keyword']]['num'])) {
                $arr[$item['keyword']]['num'] += $item['num'];
            } else {
                $arr[$item['keyword']]['num'] = $item['num'];
            }
        }

        foreach ($arr as $v) {
            $str .= $v['keyword'] . "(" . $v['num'] . ") ";
        }

        return $str;
    }

    public static function getToDaySearch()
    {
        $info = self::find()
            ->where(['day' => strtotime(date('Ymd'))])
            ->asArray()
            ->all();

        $str = '';
        $arr = [];
        foreach ($info as $item) {
            $arr[$item['keyword']]['keyword'] = KeyWords::getKeyWordById($item['keyword']);
            if (isset($arr[$item['keyword']]['num'])) {
                $arr[$item['keyword']]['num'] += $item['num'];
            } else {
                $arr[$item['keyword']]['num'] = $item['num'];
            }

        }

        foreach ($arr as $v) {
            $str .= $v['keyword'] . "(" . $v['num'] . ") ";
        }

        return $str;
    }
}
