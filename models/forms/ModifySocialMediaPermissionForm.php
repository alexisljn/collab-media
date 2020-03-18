<?php

namespace app\models\forms;

use yii\base\Model;

/**
 * ModifySocialMediaPermissionForm is the model behind the modify social media form.
 */
class ModifySocialMediaPermissionForm extends Model
{
    public $facebook_enabled;
    public $twitter_enabled;
    public $linkedin_enabled;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['facebook_enabled', 'twitter_enabled', 'linkedin_enabled',], 'required'],
        ];
    }

    public function validate($attributeNames = null, $clearErrors = true)
    {
        if (!$this->facebook_enabled && !$this->linkedin_enabled && !$this->twitter_enabled) {
            $this->addError('linkedin_enabled','You have to select at least 1 social media');
        }

        return parent::validate($attributeNames, false); // TODO: Change the autogenerated stub
    }
}