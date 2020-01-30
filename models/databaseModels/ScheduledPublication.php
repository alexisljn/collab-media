<?php

namespace app\models\databaseModels;

use Yii;

/**
 * This is the model class for table "scheduled_publication".
 *
 * @property int $proposal_id
 * @property int $scheduler_id
 * @property string $record_date
 * @property string $publication_date
 *
 * @property Proposal $proposal
 * @property User $scheduler
 */
class ScheduledPublication extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'scheduled_publication';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['proposal_id', 'scheduler_id', 'record_date', 'publication_date'], 'required'],
            [['proposal_id', 'scheduler_id'], 'integer'],
            [['record_date', 'publication_date'], 'safe'],
            [['proposal_id'], 'unique'],
            [['proposal_id'], 'exist', 'skipOnError' => true, 'targetClass' => Proposal::className(), 'targetAttribute' => ['proposal_id' => 'id']],
            [['scheduler_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['scheduler_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'proposal_id' => 'Proposal ID',
            'scheduler_id' => 'Scheduler ID',
            'record_date' => 'Record Date',
            'publication_date' => 'Publication Date',
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
     * Gets query for [[Scheduler]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getScheduler()
    {
        return $this->hasOne(User::className(), ['id' => 'scheduler_id']);
    }
}
