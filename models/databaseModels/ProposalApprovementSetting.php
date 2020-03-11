<?php

namespace app\models\databaseModels;

use Yii;

/**
 * This is the model class for table "proposal_approvement_setting".
 *
 * @property string $id
 * @property int $required_review
 * @property int $approvement_percent
 */
class ProposalApprovementSetting extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'proposal_approvement_setting';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'required_review', 'approvement_percent'], 'required'],
            [['required_review', 'approvement_percent'], 'integer'],
            [['id'], 'string', 'max' => 32],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'required_review' => 'Required Review',
            'approvement_percent' => 'Approvement Percent',
        ];
    }
}
