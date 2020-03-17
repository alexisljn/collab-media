<?php
namespace app\models\forms;

use yii\base\Model;
class ForgottenPasswordForm extends Model
{
    public $email;

    public function rules()
    {
        return [
            // email is required
            [['email'], 'required'],
            // email must be a valid email
            ['email', 'email'],
        ];
    }
}
