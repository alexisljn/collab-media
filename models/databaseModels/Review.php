<?php

namespace app\models\databaseModels;

use Yii;

/**
 * This is the model class for table "review".
 *
 * @property int $id
 * @property int $reviewer_id
 * @property int $proposal_id
 * @property string $date
 * @property string $status
 *
 * @property Proposal $proposal
 * @property User $reviewer
 */
class Review extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'review';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reviewer_id', 'proposal_id', 'date', 'status'], 'required'],
            [['reviewer_id', 'proposal_id'], 'integer'],
            [['date'], 'safe'],
            [['status'], 'string', 'max' => 32],
            [['proposal_id'], 'exist', 'skipOnError' => true, 'targetClass' => Proposal::className(), 'targetAttribute' => ['proposal_id' => 'id']],
            [['reviewer_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['reviewer_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'reviewer_id' => 'Reviewer ID',
            'proposal_id' => 'Proposal ID',
            'date' => 'Date',
            'status' => 'Status',
        ];
    }

    /**
     * Gets query for [[Proposal]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProposal()
    {
        return $this->hasOne(Proposal::className(), ['id' => 'proposal_id']);
    }

    /**
     * Gets query for [[Reviewer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReviewer()
    {
        return $this->hasOne(User::className(), ['id' => 'reviewer_id']);
    }
}
