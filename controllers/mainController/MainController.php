<?php


namespace app\controllers\mainController;


use app\components\Util;
use app\models\User;
use yii\web\Controller;

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
            'login'
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
            'index' => Util::USER_ROLE_MEMBER,
            'logout' => Util::USER_ROLE_MEMBER,
        ],
    ];

    public function init()
    {
        parent::init();
        self::logoutUserIfPasswordChanged();
    }

    public function beforeAction($action)
    {
        // TODO gestion des rôles en fonction de l'action appelée

        return parent::beforeAction($action);
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