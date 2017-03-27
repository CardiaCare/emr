<?php

namespace app\modules\survey_v2\models;

use yii\db\ActiveRecord;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use yii\web\UploadedFile;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

class ResponseFile extends ActiveRecord
{
    /**
     * @var UploadedFile
     */
    public $uploadedFile;

    public function rules()
    {
        return [];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @param string $folder
     * @return bool
     */
    public function upload($folder = 'uploads/')
    {
        if ($this->validate()) {
            FileHelper::createDirectory($folder);

            $path = $folder.sha1(uniqid().time()).'.'.$this->uploadedFile->extension;

            $this->uploadedFile->saveAs($path);
            $this->type = $this->uploadedFile->extension;
            $this->path = $path;
            $this->url = Url::base(true).'/'.$path;

            $this->save();

            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public static function tableData()
    {
        return 'response_file';
    }
}
