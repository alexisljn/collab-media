<?php


namespace app\models\forms;


use app\components\Util;
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
            ['relatedFile', 'file', 'extensions' =>
                Util::UPLOADED_FILE_ALLOWED_EXTENSIONS,
                'maxSize' => 52428800],
        ];

    }
}