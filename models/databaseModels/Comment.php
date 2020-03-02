<?php

namespace app\models\databaseModels;

use Yii;

/**
 * This is the model class for table "comment".
 *
 * @property int $id
 * @property int $proposal_id
 * @property int $author_id
 * @property string $content
 * @property string $date
 * @property string|null $edited_date
 *
 * @property User $author
 * @property Proposal $proposal
 */
class Comment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'comment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['proposal_id', 'author_id', 'content', 'date'], 'required'],
            [['proposal_id', 'author_id'], 'integer'],
            [['content'], 'string'],
            [['date', 'edited_date'], 'safe'],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['author_id' => 'id']],
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
            'author_id' => 'Author ID',
            'content' => 'Content',
            'date' => 'Date',
            'edited_date' => 'Edited Date',
        ];
    }

    /**
     * Gets query for [[Author]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(User::className(), ['id' => 'author_id']);
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
