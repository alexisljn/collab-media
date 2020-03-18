<?php
namespace app\models\forms;

use app\models\User;
use yii\base\Model;

class ResetPasswordForm extends Model
{

    public int $id;

    public function rules()
    {
        return [
            // id is required
            [['id'], 'required'],
            // id must be an integer
            ['id', 'integer'],
            ];
    }
}