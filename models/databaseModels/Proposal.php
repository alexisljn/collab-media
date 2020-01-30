<?php

namespace app\models\databaseModels;

use Yii;

/**
 * This is the model class for table "proposal".
 *
 * @property int $id
 * @property string $title
 * @property string $date
 * @property int $submitter_id
 * @property string|null $social_media social medias where the proposal will be published
 * @property bool $published
 *
 * @property Comment[] $comments
 * @property File[] $files
 * @property User $submitter
 * @property ProposalContentHistory[] $proposalContentHistories
 * @property Review[] $reviews
 * @property ScheduledPublication $scheduledPublication
 */
class Proposal extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'proposal';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'date', 'submitter_id'], 'required'],
            [['date'], 'safe'],
            [['submitter_id'], 'integer'],
            [['published'], 'boolean'],
            [['title', 'social_media'], 'string', 'max' => 255],
            [['submitter_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['submitter_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'date' => 'Date',
            'submitter_id' => 'Submitter ID',
            'social_media' => 'Social Media',
            'published' => 'Published',
        ];
    }

    /**
     * Gets query for [[Comments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['proposal_id' => 'id']);
    }

    /**
     * Gets query for [[Files]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFiles()
    {
        return $this->hasMany(File::className(), ['proposal_id' => 'id']);
    }

    /**
     * Gets query for [[Submitter]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubmitter()
    {
        return $this->hasOne(User::className(), ['id' => 'submitter_id']);
    }

    /**
     * Gets query for [[ProposalContentHistories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProposalContentHistories()
    {
        return $this->hasMany(ProposalContentHistory::className(), ['proposal_id' => 'id']);
    }

    /**
     * Gets query for [[Reviews]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReviews()
    {
        return $this->hasMany(Review::className(), ['proposal_id' => 'id']);
    }

    /**
     * Gets query for [[ScheduledPublication]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getScheduledPublication()
    {
        return $this->hasOne(ScheduledPublication::className(), ['proposal_id' => 'id']);
    }
}
