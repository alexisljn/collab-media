<?php

namespace app\models\forms;

use yii\base\Model;

/**
 * ModifyAccountForm is the model behind the modify account form.
 */
class ModifyAccountForm extends Model
{
    public $firstname;
    public $lastname;
    public $email;
    public $role;
    public $is_active;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['firstname','lastname', 'email','role', 'is_active'], 'required'],
            // email has to be a valid email address
            ['email', 'email'],
            // name length
            [['firstname'], 'string', 'max' => 32],
            [['lastname'], 'string', 'max' => 64],
            // role has to be in the roles defined
            ['role', 'validateRole'],

        ];
    }

    public function validateRole($attribute)
    {
        if (!in_array($this->role, User::USER_ROLES)) {
            $this->addError($attribute, 'Invalid Role');
        }
    }

}