<?php


namespace app\components\socialMediaApi;


use app\models\exceptions\CannotAddMediaToTweetException;
use app\models\exceptions\TwitterAPIException;
use app\models\exceptions\FileDoesNotExistException;
use app\models\exceptions\FileException;
use app\models\exceptions\TwitterAPIInvalidFileContentException;

class TwitterConnector
{
    private const BASE_URL = "https://api.twitter.com/1.1/";
    private const MEDIA_UPLOAD_BASE_URL = "https://upload.twitter.com/1.1/";
    private const POST_TWEET_URL = self::BASE_URL . 'statuses/update.json';
    private const POST_MEDIA_URL = self::MEDIA_UPLOAD_BASE_URL . 'media/upload.json';

    /**
     *
     * @var string[]
     */
    private $mediaIds = [];

    /**
     * Adds a media to a tweet
     *
     * A tweet can contain one GIF/video OR up to 4 images
     * @param string $filePath
     * @return $this
     * @throws CannotAddMediaToTweetException
     * @throws FileDoesNotExistException
     * @throws FileException
     * @throws TwitterAPIException
     * @throws TwitterAPIInvalidFileContentException
     */
    public function addMedia(string $filePath)
    {
        if(!file_exists($filePath)) {
            throw new FileDoesNotExistException('File not found');
        }

        if(count($this->mediaIds) >= 4) {
            throw new CannotAddMediaToTweetException('A tweet cannot contain more than 4 medias');
        }

        $fileSize = filesize($filePath);
        if($fileSize === false) {
            throw new FileException('Cannot read file size');
        }

        $fileMimeType = mime_content_type($filePath);

        $initializeRequestResult = $this->performRequest(self::POST_MEDIA_URL, 'POST', [
            'command' => 'INIT',
            'total_bytes' => $fileSize,
            'media_type' => $fileMimeType,
        ]);

        $mediaId = $initializeRequestResult['response']['media_id'];

        $this->mediaIds[] = $mediaId;

        $appendRequestResult = $this->performRequest(self::POST_MEDIA_URL, 'POST', [
            'command' => 'APPEND',
            'media_id' => $mediaId,
            'segment_index' => 0,
            'media_data' => base64_encode(file_get_contents($filePath)),
        ]);

        $finalizeRequestResult = $this->performRequest(self::POST_MEDIA_URL, 'POST', [
            'command' => 'FINALIZE',
            'media_id' => $mediaId,
        ]);

        if($finalizeRequestResult['statusCode'] === 400 && $finalizeRequestResult['response']['error'] === 'InvalidContent.') {
            throw new TwitterAPIInvalidFileContentException('The file you are attempting to upload has invalid content');
        }

        return $this;
    }

    /**
     * Posts the tweet
     *
     * @param string $tweetText
     * @return array
     * @throws TwitterAPIException
     */
    public function postTweet(string $tweetText)
    {
        return $this->performRequest(self::POST_TWEET_URL, 'POST', [
            'status' => $tweetText,
            'media_ids' => implode(',', $this->mediaIds),
        ]);
    }

    private function getTwitterAPIExchange()
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

    private function performRequest(string $url, string $method, array $fields = []) {
        $request = $this->getTwitterAPIExchange()
            ->buildOauth($url, $method);

        switch($method) {
            case 'GET':
                $request->setGetfield($fields);
                break;
            case 'POST':
                $request->setPostfields($fields);
                break;
            default:
                throw new \Exception('Method must be GET or POST');
        }

        if($request->getHttpStatusCode() === 401) {
            throw new TwitterAPIException('Authentication failed (HTTP status 401)');
        }

        $rawResult = $request->performRequest();

        return [
            'statusCode' => $request->getHttpStatusCode(),
            'response' => json_decode($rawResult, JSON_OBJECT_AS_ARRAY),
        ];
    }
}