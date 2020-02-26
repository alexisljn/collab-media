<?php


namespace app\models;


class Proposal extends \app\models\databaseModels\Proposal
{
    public const STATUS_PENDING = "pending";
    public const STATUS_PUBLISHED = "published";
    public const STATUS_REJECTED = "rejected";
}