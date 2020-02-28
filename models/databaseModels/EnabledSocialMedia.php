<?php

namespace app\models\databaseModels;

use Yii;

/**
 * This is the model class for table "enabled_social_media".
 *
 * @property string $social_media_name
 * @property bool $is_enabled
 */
class EnabledSocialMedia extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'enabled_social_media';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['social_media_name'], 'required'],
            [['is_enabled'], 'boolean'],
            [['social_media_name'], 'string', 'max' => 32],
            [['social_media_name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'social_media_name' => 'Social Media Name',
            'is_enabled' => 'Is Enabled',
        ];
    }
}
