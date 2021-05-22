<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "re_kw_info".
 *
 * @property integer $id
 * @property string $keyword
 * @property string $title
 * @property string $description
 * @property string $imgurl
 * @property string $url
 * @property integer $create_time
 * @property integer $update_time
 */
class ReKwInfo extends \yii\db\ActiveRecord
{
    public $imgfile;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 're_kw_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['create_time', 'update_time'], 'integer'],
            [['keyword', 'title'], 'string', 'max' => 128],
            [['description', 'imgurl', 'url'], 'string', 'max' => 256],
            [['imgfile'], 'file', 'wrongExtension' => '只能上传{extensions}格式的文件！', 'skipOnEmpty' => true , 'extensions' => 'png, jpg, jpeg, gif']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'keyword' => '关键词',
            'title' => '标题',
            'description' => '描述',
            'url' => '跳转链接',
            'imgurl' => '缩略图',
            'imgfile' => '上传缩略图',
            'create_time' => '创建时间',
            'update_time' => '修改时间',
        ];
    }

    /**
     * @return string
     */
    public function getImgUrl()
    {
        return HOST_UPLOADS_PATH . $this->imgurl;
    }
}
