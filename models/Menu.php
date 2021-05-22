<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "menu".
 *
 * @property integer $id
 * @property string $name
 * @property string $url
 * @property integer $par_btn
 * @property integer $create_time
 * @property integer $update_time
 */
class Menu extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'menu';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['par_btn', 'create_time', 'update_time'], 'integer'],
            [['name', 'url'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '按钮名称',
            'url' => '链接地址',
            'par_btn' => '父级按钮',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }

    public function getAllBtn() {
        $info = self::find()
            ->select(['name', 'id', 'par_btn'])
            ->asArray()
            ->all();

        $arr = [];
        $arr[0] = '无父级按钮';
        foreach ($info as $item) {
            if ($item['par_btn'] == 0) {
                $arr[$item['id']] = $item['name'];
            }
        }
        return $arr;
    }

    public static function getWxBtn()
    {
        $info = self::find()
            ->asArray()
            ->all();

        $arr = [];
        foreach ($info as $item) {
            if ($item['par_btn'] == 0) {
                $tmp = [
                    'type' => 'view',
                    'name' => $item['name'],
                    'url' => $item['url']
                ];
                $arr[$item['id']] = $tmp;
            } else {
                if (isset($arr[$item['par_btn']]['type'])) {
                    unset($arr[$item['par_btn']]['type']);
                }
                if (isset($arr[$item['par_btn']]['url'])) {
                    unset($arr[$item['par_btn']]['url']);
                }
                $arr[$item['par_btn']]['sub_button'][] = [
                    'type' => 'view',
                    'name' => $item['name'],
                    'url' => $item['url']
                ];
            }
        }

        foreach ($arr as $k => $value) {
            if (isset($value['sub_button'])) {
                $arr[$k]['sub_button'] = array_reverse($arr[$k]['sub_button']);
            }
        }

        $arr2 = array_values($arr);
        return $arr2;
    }

    public static function getBtnName($id)
    {
        $btn = self::findOne(['id' => $id]);
        if ($btn) {
            return $btn->name;
        }
        return '无按钮';
    }
}
