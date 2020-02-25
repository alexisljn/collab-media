<?php

namespace app\models;

use yii\base\Model;

/**
 * ModifyAccountForm is the model behind the modify account form.
 */
class ModifyAccountForm extends Model
{
    public $firstname;
    public $lastname;
    public $email;
    public $is_validated;
    public $is_active;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['firstname','lastname', 'email', 'is_validated', 'is_active'], 'required'],
            // email has to be a valid email address
            ['email', 'email'],
        ];
    }

}