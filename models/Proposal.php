<?php


namespace app\models;


class Proposal extends databaseModels\Proposal
{
    public $count_reviews = null;
    public const STATUS_PENDING = "pending";
    public const STATUS_PUBLISHED = "published";
    public const STATUS_REJECTED = "rejected";

}