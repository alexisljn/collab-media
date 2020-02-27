<?php
namespace app\models\forms;

use yii\base\Model;

class ModifySocialMediaInformationsForm extends Model
{
    public $is_enabled;

    public function rules()
    {
        return [
            // social_media_name and is_enabled are required
            ['is_enabled', 'required'],
        ];
    }
}