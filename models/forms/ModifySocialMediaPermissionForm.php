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
            // facebook_enabled, twitter_enabled and linkedin_enabled are required
            [['facebook_enabled', 'twitter_enabled', 'linkedin_enabled',], 'required'],
        ];
    }
}