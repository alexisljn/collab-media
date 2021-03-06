<?php

namespace app\models\forms;

use yii\base\Model;

/**
 * ChangePasswordForm is the model behind the forgotten password form.
 * Class ValidateAccountForm
 * @package app\models\forms
 */
class ChangePasswordForm extends Model
{
    public $password;
    public $confirmPassword;

    public function rules()
    {
        return [
            // password and confirmPassword are both required
            [['password', 'confirmPassword'], 'required'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
            // password and confirmPassword must match
            ['confirmPassword', 'compare', 'compareAttribute' => 'password', 'message' => 'Password and password confirmation fields don\'t match.'],
        ];
    }

    public function validatePassword($attribute)
    {
        if(mb_strlen($this->password) < 8) {
            $this->addError($attribute, 'Password must be at least 8 characters long.');
        }
    }
}