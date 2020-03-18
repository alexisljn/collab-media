<?php

namespace app\controllers;

use app\components\Util;
use app\controllers\mainController\MainController;
use app\models\exceptions\CannotCreateTokenException;
use app\models\exceptions\CannotSaveException;
use app\models\exceptions\CannotSendMailException;
use app\models\forms\ChangePasswordForm;
use app\models\forms\ForgottenPasswordForm;
use app\models\forms\ValidateAccountForm;
use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\forms\LoginForm;

class SiteController extends MainController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     * @throws \Exception
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            MainController::afterLogin();
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * @param null $token
     * @return string
     * @throws \yii\base\Exception
     * @throws CannotSaveException
     */
    public function actionValidateAccount($token = null)
    {
        $user = $this->checkIfUserMatchToToken($token);

        $form = new ValidateAccountForm();

        if($form->load($_POST) && $form->validate()) {
            $this->validateAccount($form, $user);
        }

        return $this->render('validate-account', [
           'model' => $form,
        ]);
    }

    /**
     * @param $form
     * @param $user
     * @throws CannotSaveException
     * @throws \yii\base\Exception
     */
    private function validateAccount($form, $user)
    {
        $user->password_hash = Yii::$app->security->generatePasswordHash($form->password);
        $user->is_validated = true;
        $user->token = null;

        if (!$user->save()) {
            throw new CannotSaveException($user);
        }

        Yii::$app->user->login($user);
        $_SESSION["userPasswordHash"] = $user->password_hash;
        return $this->redirect("/");
    }

    /**
     * @param $token
     * @return User|null
     */
    private function checkIfUserMatchToToken($token)
    {
        $notFoundException = NotFoundHttpException::class;
        if (!is_null($user = User::findOne(['token' => $token]))) {
            return $user;
        }
        throw new $notFoundException();
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function actionForgottenPassword()
    {
        $form = new ForgottenPasswordForm();

        if($form->load($_POST) && $form->validate()) {
            $this->forgetPassword($form);
        }

        return $this->render('forgotten-password', [
            'forgottenPasswordModel' => $form,
        ]);
    }

    /**
     * @param $form
     * @throws \Exception
     */
    private function forgetPassword($form)
    {
        $user = $this->checkIfUserMatchToEmail($form->email);

        $tokenTry = 0;
        do {
            if($tokenTry === 10) {
                throw new CannotCreateTokenException();
            }
            $token = $this->createUserToken();
            $tokenTry++;
        } while (!is_null(User::findOne(['token' => $token])));

        $user->token = $token;

        if (!$user->save()) {
            throw new CannotSaveException($user);
        }
        $this->mailToUserPasswordForgotten($user);

        return $this->redirect("/");
    }

    /**
     * @param null $token
     * @return string
     * @throws \yii\base\Exception
     * @throws CannotSaveException
     */
    public function actionChangePassword($token = null)
    {
        $user = $this->checkIfUserMatchToToken($token);

        $form = new ChangePasswordForm();

        if($form->load($_POST) && $form->validate()) {
            $this->changePassword($form, $user);
        }

        return $this->render('change-password', [
            'model' => $form,
        ]);
    }

    /**
     * @param $form
     * @param $user
     * @throws CannotSaveException
     * @throws \yii\base\Exception
     */
    private function changePassword($form, $user)
    {
        $user->password_hash = Yii::$app->security->generatePasswordHash($form->password);
        $user->token = null;

        if (!$user->save()) {
            throw new CannotSaveException($user);
        }

        Yii::$app->user->login($user);
        $_SESSION["userPasswordHash"] = $user->password_hash;
        return $this->redirect("/");
    }

    /**
     * @param $email
     * @return User|null
     * @throws \Exception
     */
    private function checkIfUserMatchToEmail($email)
    {
        $notFoundException = NotFoundHttpException::class;
        if (!is_null($user = User::findOne(['email' => $email]))) {
            return $user;
        }

        throw new $notFoundException();
    }

    /**
     * @param User $user
     * @return bool
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws CannotSendMailException
     */
    private function mailToUserPasswordForgotten(User $user)
    {
        $mail = Util::getConfiguredMailerForMailhog();
        $mail->addAddress($user->email);
        $mail->isHTML(false);
        $mail->CharSet = 'UTF-8';

        $mail->Subject = "Forgotten your Collab'media account password";
        $mail->Body = 'Click on the following link to reset your password : '. Util::BASE_URL .'/site/change-password/' . $user->token ;

        if(!$mail->send()) {
            throw new CannotSendMailException();
        }
    }
    /**
     * @throws \Exception
     */
    private function createUserToken()
    {
        return Util::getRandomString(32);
    }
}
