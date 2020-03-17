<?php


namespace app\models\forms;


use yii\base\Model;

class PublishProposalForm extends Model
{
    const TWEET_MAX_CHARS = 280;
    public $content;
    public $file;
    public $social_media;

    public function rules()
    {
        return [
            [['content', 'social_media'], 'required'],
            ['content', 'validateContent'],
        ];
    }

    public function validateContent($attribute)
    {
        if (mb_strlen($this->content) > self::TWEET_MAX_CHARS) {
            $this->addError($attribute, 'Too long content');
        }
    }
}