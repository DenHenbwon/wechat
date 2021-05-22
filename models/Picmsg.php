<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "picmsg".
 *
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property string $author
 * @property string $source_url
 * @property integer $source_id
 * @property string $content
 * @property integer $status
 * @property integer $create_time
 * @property integer $update_time
 * @property integer $send_time
 * @property integer $is_delete
 * @property integer $show_cover_pic
 */
class Picmsg extends \yii\db\ActiveRecord
{
    const STATUS_IS_DEFAULT = 0;

    const SHOW_COVER_PIC_NO = 0;
    const SHOW_COVER_PIC_YES = 1;

    const IS_DELETE_FALSE = 0;
    const IS_DELETE_TRUE = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'picmsg';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['source_id', 'is_delete', 'status', 'create_time', 'update_time', 'send_time', 'show_cover_pic'], 'integer'],
            [['title', 'author'], 'string', 'max' => 128],
            [['description'], 'string', 'max' => 255],
            [['content'], 'string', 'max' => 2048],
            [['source_url'], 'string', 'max' => 1024],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '标题',
            'author' => '作者',
            'source_url' => '阅读更多url',
            'description' => '描述',
            'source_id' => '素材ID',
            'content' => '内容',
            'status' => '状态',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'send_time' => '发送时间',
            'show_cover_pic' => '缩略图是否作封面显示',
            'is_delete' => '是否删除',
        ];
    }

    public static function getStatusList()
    {
        return [
            self::STATUS_IS_DEFAULT => '默认',
        ];
    }

    public function getStatusLabel()
    {
        return self::getStatusList()[$this->status];
    }

    public static function getShowCoverPicList()
    {
        return [
            self::SHOW_COVER_PIC_NO => '不显示',
            self::SHOW_COVER_PIC_YES => '显示',
        ];
    }

    public function getShowCoverPicLabel()
    {
        return self::getShowCoverPicList()[$this->show_cover_pic];
    }

    public function judgeSourceIsExpire()
    {
        $sourceInfo = Source::findOne($this->source_id);
        if (empty($sourceInfo)) {
            return false;
        }

        $expire_time = time() - PUSH_THREE_DAYS_DIFF_TIME;
        if ($expire_time < $sourceInfo->upload_time) {
            return true;
        }
        return false;
    }
}
