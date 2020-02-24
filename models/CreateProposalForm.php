<?php


namespace app\models;


use yii\base\Model;

class CreateProposalForm extends Model
{
    public $title;
    public $content;
    public $relatedFile;

    public function rules()
    {
        return [
            [['title', 'content'], 'required'],
            ['relatedFile', 'file', 'extensions' => ['png', 'jpg', 'jpeg', 'gif'], 'maxSize' => 52428800],
        ];
    }
}