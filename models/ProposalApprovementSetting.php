<?php


namespace app\models;


class ProposalApprovementSetting extends \app\models\databaseModels\ProposalApprovementSetting
{
    public const MAIN_SETTING = "main";

    public static function getApprovementPercent()
    {
        return self::findOne(['id' => self::MAIN_SETTING])->approvement_percent;
    }

    public static function getRequiredNumberOfReview()
    {
        return self::findOne(['id' => self::MAIN_SETTING])->required_review;
    }
}