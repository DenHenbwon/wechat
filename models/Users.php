<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property integer $uid
 * @property string $open_id
 * @property string $nick_name
 * @property string $remark_name
 * @property integer $sex
 * @property string $language
 * @property string $country
 * @property string $province
 * @property string $city
 * @property string $headimgurl
 * @property integer $subscribe_time
 * @property integer $groupid
 * @property integer $status
 * @property string $tagid_list
 */
class Users extends \yii\db\ActiveRecord
{
    const SEX_MALE = 1;
    const SEX_WOMAN = 2;
    const SEX_UNKNOWN = 0;

    const STATUS_FOLLOW = 0;//正在关注用户
    const STATUS_UNFOLLOW = 1;//取消关注用户

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sex', 'subscribe_time', 'groupid', 'status'], 'integer'],
            [['open_id'], 'string', 'max' => 28],
            [['nick_name', 'remark_name'], 'string', 'max' => 255],
            [['language', 'country', 'province', 'city', 'tagid_list'], 'string', 'max' => 128],
            [['headimgurl'], 'string', 'max' => 1024],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'uid' => 'Uid',
            'open_id' => 'Open ID',
            'nick_name' => '昵称',
            'remark_name' => '备注的名称',
            'sex' => '性别',
            'language' => '语言',
            'country' => '所在国家',
            'province' => '所在省份',
            'city' => '所在城市',
            'headimgurl' => '头像链接',
            'subscribe_time' => '订阅时间',
            'groupid' => '组ID',
            'tagid_list' => '标签ID',
            'status' => '状态',
        ];
    }

    public function getUserSex()
    {
        switch ($this->sex) {
            case self::SEX_MALE:
                return '男';
                break;

            case self::SEX_WOMAN:
                return '女';
                break;

            default:
                return '未设置';
                break;
        }
    }
}
