<?php


namespace app\models\forms;


use app\components\Util;
use yii\base\Model;

class ManageProposalForm extends Model
{
    public $title;
    public $content;
    public $relatedFile;

    public function rules()
    {
        return [
            [['title', 'content'], 'required'],
            ['relatedFile', 'file', 'extensions' =>
                Util::UPLOADED_FILE_RULES,
                'maxSize' => 15000000],
        ];

    }
}