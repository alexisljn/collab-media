<?php
namespace app\models;

use yii\base\Model;


/**
 * Class CreateAccountForm
 * @package app\models
 */
class CreateAccountForm extends Model
{
    public $firstname;
    public $lastname;
    public $email;
    public $role;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['firstname', 'lastname', 'email', 'role'], 'required'],
            // email has to be a valid email address
            ['email', 'email'],
        ];
    }
}