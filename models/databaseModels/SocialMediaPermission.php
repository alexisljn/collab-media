<?php

namespace app\models\databaseModels;

use Yii;

/**
 * This is the model class for table "social_media_permission".
 *
 * @property int $publisher_id
 * @property bool $facebook_enabled
 * @property bool $twitter_enabled
 * @property bool $linkedin_enabled
 *
 * @property User $publisher
 */
class SocialMediaPermission extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'social_media_permission';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['publisher_id'], 'required'],
            [['publisher_id'], 'integer'],
            [['facebook_enabled', 'twitter_enabled', 'linkedin_enabled'], 'boolean'],
            [['publisher_id'], 'unique'],
            [['publisher_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['publisher_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'publisher_id' => 'Publisher ID',
            'facebook_enabled' => 'Facebook Enabled',
            'twitter_enabled' => 'Twitter Enabled',
            'linkedin_enabled' => 'Linkedin Enabled',
        ];
    }

    /**
     * Gets query for [[Publisher]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPublisher()
    {
        return $this->hasOne(User::className(), ['id' => 'publisher_id']);
    }
}
