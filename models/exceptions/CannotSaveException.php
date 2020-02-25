<?php


namespace app\models\exceptions;


use Throwable;
use yii\db\ActiveRecord;

class CannotSaveException extends \Exception
{
    public function __construct(ActiveRecord $failedToSave ,$message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}