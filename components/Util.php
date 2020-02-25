<?php


namespace app\components;


use app\models\exceptions\StaticClassNotInstantiableException;

class Util
{
    public const ALLOWED_EXTENSIONS = ['png', 'jpg', 'jpeg', 'gif', 'mp3', 'mp4', 'mov', 'pdf', 'zip' ];

    public function __construct()
    {
        throw new StaticClassNotInstantiableException();
    }

    /**
     * Converts a DateTimeInterface into a string to save it in database
     *
     * @param \DateTimeInterface $dateTime the datetime to convert
     * @return string the converted datetime
     */
    public static function getDateTimeFormattedForDatabase(\DateTimeInterface $dateTime): string
    {
        return $dateTime->format('Y-m-d H:i:s');
    }

    /**
     * Converts a string dateTime to a DateTimeObject
     *
     * @param string $stringDateTime the string dateTime to get as DateTime object
     * @param bool $getAsImmutable if true, a {@see \DateTimeImmutable} will be returned. Otherwise, it will be a {@see \DateTime}
     * @return \DateTimeInterface
     */
    public static function getDateTimeFromDatabaseString(string $stringDateTime, bool $getAsImmutable = false): \DateTimeInterface
    {
        if($getAsImmutable) {
            $class = \DateTimeImmutable::class;
        } else {
            $class = \DateTime::class;
        }
        /** @var \DateTime|\DateTimeImmutable $class */

        $object = $class::createFromFormat('Y-m-d H:i:s', $stringDateTime);
        if($object === false) {
            throw new \InvalidArgumentException('String ' . $stringDateTime . ' is not valid');
        }

        return $object;
    }
}