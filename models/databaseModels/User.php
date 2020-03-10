<?php

namespace app\models\databaseModels;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $firstname
 * @property string $lastname
 * @property string $email
 * @property string|null $password_hash
 * @property string $role
 * @property bool $is_validated true if user validated his account by choosing his password
 * @property bool $is_active true if account is active by admin
 * @property string $token
 *
 * @property Comment[] $comments
 * @property Proposal[] $proposals
 * @property Review[] $reviews
 * @property ScheduledPublication[] $scheduledPublications
 * @property SocialMediaPermission $socialMediaPermission
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['firstname', 'lastname', 'email', 'role', 'token'], 'required'],
            [['is_validated', 'is_active'], 'boolean'],
            [['firstname', 'role'], 'string', 'max' => 32],
            [['lastname'], 'string', 'max' => 64],
            [['email', 'password_hash'], 'string', 'max' => 255],
            [['email', 'token'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'firstname' => 'Firstname',
            'lastname' => 'Lastname',
            'email' => 'Email',
            'password_hash' => 'Password Hash',
            'role' => 'Role',
            'is_validated' => 'Is Validated',
            'is_active' => 'Is Active',
            'token' => 'User Token',
        ];
    }

    /**
     * Gets query for [[Comments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['author_id' => 'id']);
    }

    /**
     * Gets query for [[Proposals]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProposals()
    {
        return $this->hasMany(Proposal::className(), ['submitter_id' => 'id']);
    }

    /**
     * Gets query for [[Reviews]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReviews()
    {
        return $this->hasMany(Review::className(), ['reviewer_id' => 'id']);
    }

    /**
     * Gets query for [[ScheduledPublications]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getScheduledPublications()
    {
        return $this->hasMany(ScheduledPublication::className(), ['scheduler_id' => 'id']);
    }

    /**
     * Gets query for [[SocialMediaPermission]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSocialMediaPermission()
    {
        return $this->hasOne(SocialMediaPermission::className(), ['publisher_id' => 'id']);
    }
}
