<?php


namespace app\models\forms;


use yii\base\Model;

class ManageCommentForm extends Model
{
    public $content;

    public function rules()
    {
        return [
          [['content'], 'required']
        ];
    }
}