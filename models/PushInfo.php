<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "push_info".
 *
 * @property integer $push_id
 * @property integer $show_cover_picmsg_id
 * @property string $push_detail
 * @property string $media_id
 * @property integer $type
 * @property string $msg_id
 * @property string $msg_data_id
 * @property integer $create_time
 * @property integer $update_time
 * @property integer $push_time
 * @property integer $status
 * @property integer $is_delete
 */
class PushInfo extends \yii\db\ActiveRecord
{
    const STATUS_IS_UNPUSH = 0;
    const STATUS_IS_PUSHED = 1;

    const IS_DELETE_FALSE = 0;
    const IS_DELETE_TRUE = 1;

    const TYPE_NEWS = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'push_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['show_cover_picmsg_id', 'create_time', 'update_time', 'push_time', 'is_delete', 'status', 'type'], 'integer'],
            [['push_detail'], 'string', 'max' => 2048],
            [['media_id'], 'string', 'max' => 255],
            [['msg_id', 'msg_data_id',], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'push_id' => '推送列表ID',
            'show_cover_picmsg_id' => '封面文章ID',
            'push_detail' => '推送文章详情',
            'media_id' => 'Media ID',
            'type' => '类型',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'push_time' => '推送时间',
            'status' => '状态',
        ];
    }

    public static function getPushStatus()
    {
        return [
            self::STATUS_IS_UNPUSH => '待推送',
            self::STATUS_IS_PUSHED => '已推送',
        ];
    }

    public function getPushStatusLabel()
    {
        return self::getPushStatus()[$this->status];
    }
}
