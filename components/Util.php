<?php


namespace app\components;


use app\models\exceptions\StaticClassNotInstantiableException;
use PHPMailer\PHPMailer\PHPMailer;

class Util
{
    public const UPLOADED_FILE_RULES = [
        'png' => '5000000',
        'jpg' => '5000000',
        'jpeg' => '5000000',
        'gif' => '15000000',
        'mp4' => '15000000',
    ];
    public const RANDOM_STRING_ALPHABET_LOWERCASE = 'abcdefghijklmnopqrstuvwxyz';
    public const RANDOM_STRING_ALPHABET_UPPERCASE = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    public const RANDOM_STRING_NUMERIC = "0123456789";
    public const RANDOM_STRING_ALPHANUMERIC_ALL_CASE = self::RANDOM_STRING_ALPHABET_LOWERCASE . self::RANDOM_STRING_ALPHABET_UPPERCASE . self::RANDOM_STRING_NUMERIC;

    public const BASE_URL = "http://127.0.0.1:8000";

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

    /**
     * @param bool $enableExceptions
     * @return PHPMailer
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public static function getConfiguredMailerForMailhog(bool $enableExceptions = true): PHPMailer
    {
        $phpMailer = new PHPMailer($enableExceptions);

        $phpMailer->Host = 'mailhog';
        $phpMailer->isSMTP();
        $phpMailer->SMTPAuth = false;
        $phpMailer->Port = '1025';

        $phpMailer->setFrom('no-reply@collab-media.com');

        return $phpMailer;
    }

    /**
     * @param $length
     * @param string $characters
     * @return string
     * @throws \Exception
     */
    public static function getRandomString($length, $characters = self::RANDOM_STRING_ALPHANUMERIC_ALL_CASE)
    {
        $string = '';
        for($i = 0; $i < $length; ++$i) {
            $string .= $characters[random_int(0, mb_strlen($characters)-1)];
        }
        return $string;
    }

    /**
     * Deletes an element by key from an array and re-order the keys (the output array has ordered numeric keys)
     * @param array $array
     * @param $key
     */
    public static function deleteElementFromArray(array &$array, $key)
    {
        unset($array[$key]); // Unset function deletes the element, but does not re-order keys
        $array = array_values($array); // Re-order keys
    }
}