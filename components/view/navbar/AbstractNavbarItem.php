<?php


namespace app\components\view\navbar;


use app\controllers\mainController\MainController;
use yii\base\InvalidConfigException;

/**
 * Base class for clickable items in Navbar
 * @package app\components\view\navbar
 */
abstract class AbstractNavbarItem
{
    /**
     * @var string $title The text displayed for the item
     */
    protected $title;

    /**
     * @var string $url The URL of the item
     */
    protected $url;

    /**
     * @var bool $isDisplayed If the item is displayed or not, according to the user role
     */
    protected $isDisplayed;

    /**
     * @var bool $isActive If the item is displayed as active, according to the called action
     */
    protected $isActive;

    /**
     * AbstractNavbarItem constructor
     * @param array $config the item config (see {@see \app\components\view\navbar\Navbar::__construct} for details)
     * @throws InvalidConfigException
     */
    public function __construct(array $config)
    {
        if(!isset($config['title'])) {
            throw new InvalidConfigException('NavbarItem title must be set');
        }

        if(!isset($config['url'])) {
            throw new InvalidConfigException('NavbarItem URL must be set');
        }

        $this->title = $config['title'];
        $this->url = $config['url'];

        // Determines whether the item should be displayed or not
        if(array_key_exists('roleNeeded', $config)) {
            if(\Yii::$app->user->isGuest) {
                $this->isDisplayed = false;
            } else {
                // If a role is needed, the item is displayed if the current user has the needed role
                $this->isDisplayed = MainController::getCurrentUser()->hasRole($config['roleNeeded']);
            }
        } else {
            // If no role is needed, the item is always displayed
            $this->isDisplayed = true;
        }

        // Determines whether the item should be active or not
        if(array_key_exists('activeActions', $config) && is_array($config['activeActions'])) {
            $currentControllerName = \Yii::$app->controller->id;
            $currentActionName = \Yii::$app->controller->action->id;

            if(array_key_exists($currentControllerName, $config['activeActions'])) {
                $this->isActive = $config['activeActions'][$currentControllerName] === '*' || in_array($currentActionName, $config['activeActions'][$currentControllerName]);
            } else {
                $this->isActive = false;
            }
        } else {
            // If no active actions are defined, the item is never active
            $this->isActive = false;
        }
    }

    /**
     * Returns the HTML of the item
     * @return string
     */
    abstract public function getHTML(): string;
}