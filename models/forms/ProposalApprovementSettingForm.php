<?php


namespace app\models\forms;


use yii\base\Model;

class ProposalApprovementSettingForm extends Model
{
    public $required_review;
    public $approvement_percent;

    public function rules()
    {

        return [
            [['required_review', 'approvement_percent'], 'required'],
            ['required_review', 'integer', 'min' => 1],
            [['approvement_percent'], 'integer', 'min' => 0, 'max' => 100 ]
        ];
    }

}