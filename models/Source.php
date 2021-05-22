<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "source".
 *
 * @property integer $id
 * @property string $file_name
 * @property string $media_id
 * @property string $url
 * @property integer $file_type
 * @property integer $file_size
 * @property integer $media_type
 * @property integer $upload_time
 * @property integer $create_time
 * @property integer $update_time
 */
class Source extends \yii\db\ActiveRecord
{
    public $source;

    const FILE_TYPE_IMAGE = 0;//图片类型资源

    const MEDIA_TYPE_TEMP = 0;
    const MEDIA_TYPE_FOREVER = 1;
    const MEDIA_TYPE_PUSHINFO_MATERIAL = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'source';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['file_type', 'media_type', 'upload_time', 'create_time', 'update_time', 'file_size'], 'integer'],
            [['file_name'], 'string', 'max' => 128],
            [['url'], 'string', 'max' => 2048],
            [['media_id'], 'string', 'max' => 64],
            [['source'], 'file', 'wrongExtension' => '只能上传{extensions}格式的文件！', 'skipOnEmpty' => true , 'extensions' => 'png, jpg, jpeg, gif']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'file_name' => '文件',
            'media_id' => '素材ID',
            'media_id' => '素材ID',
            'url' => '素材链接',
            'file_type' => '文件类型',
            'file_size' => '文件大小[单位:字节]',
            'media_type' => '素材类型',
            'upload_time' => '上传素材时间',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }

    public static function getFileTypeList()
    {
        return [
            self::FILE_TYPE_IMAGE => '图片素材',
        ];
    }

    public function getFileTypeLabel()
    {
        return self::getFileTypeList()[$this->file_type];
    }

    public static function getMediaTypeList($is_form = false)
    {
        $list = [
            self::MEDIA_TYPE_TEMP => '临时素材',
            self::MEDIA_TYPE_FOREVER => '永久素材',
            self::MEDIA_TYPE_PUSHINFO_MATERIAL => '图文素材',
        ];
        if ($is_form) {
            unset($list[self::MEDIA_TYPE_FOREVER]);
        }
        return $list;
    }

    public function getMediaTypeLabel()
    {
        return self::getMediaTypeList()[$this->media_type];
    }

    public function distinguishFileType($extension)
    {
        $allow_img_suffix = Yii::$app->params['allow_img_suffix'];
        if (in_array($extension, $allow_img_suffix)) {
            return self::FILE_TYPE_IMAGE;
        }
        return self::FILE_TYPE_IMAGE;
    }

    public static function getSourceInfo($id)
    {
        return self::findOne(['id' => $id]);
    }

    public static function returnMediaId($id)
    {
        $sourceInfo = self::getSourceInfo($id);
        return $sourceInfo->media_id;
    }
}
