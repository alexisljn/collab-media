<?php


namespace app\controllers\mainController;


use app\components\Util;
use app\models\User;
use yii\base\Action;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;

class MainController extends Controller
{
    /**
     * Defines the actions that are accessible to guest users
     *
     * Format :
     * 'controller-name' => [
     *     'allowed-action-name',
     * ],
     */
    private const GUEST_ACTIONS = [
        'site' => [
            'login',
            'error',
        ]
    ];

    /**
     * Defines the role needed for each action
     *
     * Format :
     * 'controller-name' => [
     *     'action-name' => 'role',
     * ],
     * 'controller-name' => [
     *     '*' => 'role', // All actions of this controller need this role
     * ]
     */
    private const ACTIONS_REQUIRED_ROLES = [
        'site' => [
            '*' => User::USER_ROLE_MEMBER,
        ],
        'proposal' => [
            'my-proposals' => User::USER_ROLE_MEMBER,
            'reviewer-pending-proposals' => User::USER_ROLE_REVIEWER
        ],
        'management' => [
            'accounts' => User::USER_ROLE_ADMIN,
        ],
    ];

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        self::logoutUserIfPasswordChanged();
    }

    /**
     * @inheritDoc
     */
    public function beforeAction($action)
    {
        $this->handleActionAuthorization($action);

        return parent::beforeAction($action);
    }

    /**
     * Handles action access according to user status (logged in or not)
     * and, if user is logged in, his role
     *
     * @param Action $action
     */
    private function handleActionAuthorization(Action $action)
    {
        if(\Yii::$app->user->isGuest) {
            $this->handleGuestActionAuthorization($action);
            return;
        }

        $this->handleLoggedInActionAuthorization($action);
    }

    /**
     * Redirects the guest user to the login page if the user
     * tries to access an action that needs to be logged in
     *
     * @param Action $action
     */
    private function handleGuestActionAuthorization(Action $action)
    {
        $actionName = $action->id;
        $controllerName = $action->controller->id;

        $redirect = true;

        if(array_key_exists($controllerName, self::GUEST_ACTIONS)) {
            if(in_array($actionName, self::GUEST_ACTIONS[$controllerName])) {
                $redirect = false;
            }
        }

        if($redirect) {
            header('location: /site/login');
            die();
        }
    }

    /**
     * Denies access to the action if the user does not
     * have the required role
     *
     * @param Action $action
     */
    private function handleLoggedInActionAuthorization(Action $action)
    {
        // The exception thrown if the user has not the required role
        $unauthorizedException = NotFoundHttpException::class;

        $actionName = $action->id;
        $controllerName = $action->controller->id;

        // Error action is always accessible
        if($controllerName === 'site' && $actionName === 'error') {
            return;
        }

        if(!array_key_exists($controllerName, self::ACTIONS_REQUIRED_ROLES)) {
            throw new $unauthorizedException();
        }

        if(!is_array(self::ACTIONS_REQUIRED_ROLES[$controllerName])) {
            throw new $unauthorizedException();
        }

        if(array_key_exists('*', self::ACTIONS_REQUIRED_ROLES[$controllerName])) {
            $requiredRole = self::ACTIONS_REQUIRED_ROLES[$controllerName]['*'];
        } else {
            if(!array_key_exists($actionName, self::ACTIONS_REQUIRED_ROLES[$controllerName])) {
                throw new $unauthorizedException();
            }

            $requiredRole = self::ACTIONS_REQUIRED_ROLES[$controllerName][$actionName];
        }

        if(!self::getCurrentUser()->hasRole($requiredRole)) {
            throw new $unauthorizedException();
        }
    }

    /**
     * Returns current logged user
     *
     * @return User|null
     */
    public static function getCurrentUser(): ?User
    {
        if(\Yii::$app->user->isGuest) {
            return null;
        }

        /** @var User $identity */
        $identity = \Yii::$app->user->identity;

        return $identity;
    }

    /**
     * Called after user login.
     * Saves user's password hash in session, in case the user changes his password
     *
     * @throws \Exception if user is not logged in
     */
    public static function afterLogin()
    {
        if(\Yii::$app->user->isGuest) {
            throw new \Exception('User is not logged in');
        }

        /** @var User $identity */
        $identity = \Yii::$app->user->identity;
        $_SESSION['userPasswordHash'] = $identity->password_hash;
    }

    /**
     * Logouts user if the password has changed
     * If user has changed his password on another computer,
     * it will not match with the one saved in session
     */
    private function logoutUserIfPasswordChanged()
    {
        if(\Yii::$app->user->isGuest) {
            return;
        }

        if(empty($_SESSION['userPasswordHash'])) {
            \Yii::$app->user->logout();
        }

        if($_SESSION['userPasswordHash'] !== self::getCurrentUser()->password_hash) {
            \Yii::$app->user->logout();
        }
    }
}