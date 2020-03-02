<?php


namespace app\components\socialMediaApi;


use app\models\exceptions\CannotTweetException;
use app\models\exceptions\StaticClassNotInstantiableException;

class TwitterConnector
{
    private const BASE_URL = "https://api.twitter.com/1.1/";
    private const POST_TWEET_URL = self::BASE_URL . 'statuses/update.json';

    public function __construct()
    {
        // This class is static and cannot be instantiated
        throw new StaticClassNotInstantiableException();
    }

    public static function post(string $tweetText)
    {
        $twitterAPIExchange = self::getTwitterAPIExchange();

        $rawResult = $twitterAPIExchange
            ->buildOauth(self::POST_TWEET_URL, 'POST')
            ->setPostfields([
                'status' => $tweetText,
            ])
            ->performRequest();

        if($twitterAPIExchange->getHttpStatusCode() === 401) {
            throw new CannotTweetException('Authentication failed (HTTP status 401)');
        }

        if(substr($twitterAPIExchange->getHttpStatusCode(), 0, 1) != '2') {
            throw new CannotTweetException('Twitter API responded with code ' . $twitterAPIExchange->getHttpStatusCode());
        }

        $result = json_decode($rawResult, JSON_OBJECT_AS_ARRAY);

        return $result;
    }

    private static function getTwitterAPIExchange()
    {
        if(!file_exists(__DIR__ . '/config/twitter.json')) {
            throw new \RuntimeException('Configuration file twitter.json not found');
        }

        $config = json_decode(file_get_contents(__DIR__ . '/config/twitter.json'), JSON_OBJECT_AS_ARRAY);

        if(!array_key_exists('consumerKey', $config)) {
            throw new \RuntimeException('consumerKey must be set in twitter.json');
        }
        if(!array_key_exists('consumerSecret', $config)) {
            throw new \RuntimeException('consumerSecret must be set in twitter.json');
        }
        if(!array_key_exists('accessToken', $config)) {
            throw new \RuntimeException('accessToken must be set in twitter.json');
        }
        if(!array_key_exists('accessTokenSecret', $config)) {
            throw new \RuntimeException('accessTokenSecret must be set in twitter.json');
        }

        return new \TwitterAPIExchange([
            'consumer_key' => $config['consumerKey'],
            'consumer_secret' => $config['consumerSecret'],
            'oauth_access_token' => $config['accessToken'],
            'oauth_access_token_secret' => $config['accessTokenSecret'],
        ]);
    }
}