<?php


namespace app\models\forms;


use yii\base\Model;

class ManageCommentForm extends Model
{
    public $content;
    public $needle;

    public function rules()
    {
        return [
          [['content'], 'required']
        ];
    }
}