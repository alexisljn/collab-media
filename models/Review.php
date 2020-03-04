<?php


namespace app\models;


class Review extends databaseModels\Review
{
    public const REVIEW_STATUS_APPROVED = 'approved';
    public const REVIEW_STATUS_DISAPPROVED = 'disapproved';
}