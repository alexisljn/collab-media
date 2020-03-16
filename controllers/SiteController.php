<?php

namespace app\controllers;

use app\controllers\mainController\MainController;
use app\models\exceptions\CannotSaveException;
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
}
