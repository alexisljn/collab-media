<?php

namespace app\models\databaseModels;

use Yii;

/**
 * This is the model class for table "proposal_file_history".
 *
 * @property int $id
 * @property int $proposal_id
 * @property string $date
 * @property string $path
 *
 * @property Proposal $proposal
 */
class ProposalFileHistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'proposal_file_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['proposal_id', 'date', 'path'], 'required'],
            [['proposal_id'], 'integer'],
            [['date'], 'safe'],
            [['path'], 'string', 'max' => 255],
            [['proposal_id'], 'exist', 'skipOnError' => true, 'targetClass' => Proposal::className(), 'targetAttribute' => ['proposal_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'proposal_id' => 'Proposal ID',
            'date' => 'Date',
            'path' => 'Path',
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
}
