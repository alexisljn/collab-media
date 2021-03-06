<?php

namespace app\models\databaseModels;

use Yii;

/**
 * This is the model class for table "file".
 *
 * @property int $id
 * @property int $proposal_id
 * @property string $path
 *
 * @property Proposal $proposal
 */
class File extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'file';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['proposal_id', 'path'], 'required'],
            [['proposal_id'], 'integer'],
            [['path'], 'string', 'max' => 255],
            [['proposal_id'], 'unique'],
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
