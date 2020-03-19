<?php
namespace app\controllers;

use app\components\Util;
use app\controllers\mainController\MainController;
use app\models\databaseModels\EnabledSocialMedia;
use app\models\databaseModels\SocialMediaPermission;
use app\models\exceptions\CannotCreateTokenException;
use app\models\exceptions\CannotSendMailException;
use app\models\forms\CreateAccountForm;
use app\models\databaseModels\User;
use app\models\exceptions\CannotSaveException;
use app\models\forms\ModifySocialMediaInformationsForm;
use app\models\forms\ModifySocialMediaPermissionForm;
use app\models\forms\ProposalApprovementSettingForm;
use app\models\Proposal;
use app\models\ProposalApprovementSetting;
use app\models\forms\ResetPasswordForm;
use PHPMailer\PHPMailer\Exception;
use yii\data\ActiveDataProvider;
use app\models\forms\ModifyAccountForm;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;


class ManagementController extends MainController
{
    /**
     * Action that allows an admin to look at all the accounts
     *
     * @param null | int $id
     * @return string
     * @throws CannotSaveException
     */
    public function actionAccounts($id = null)
    {

        if(!is_null($id)) {
            return $this->actionModifyAccount($id);
        }

        $usersDataProvider = new ActiveDataProvider([
            'query' => User::find(),
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
        return $this->render('accounts', [
            'usersDataProvider' => $usersDataProvider
        ]);
    }

    /**
     * Display a page where the account of the user selected in actionAccounts can be modified
     *
     * @param int $id
     * @return string
     * @throws CannotSaveException
     */
    private function actionModifyAccount($id)
    {
        $user = $this->checkIfUserExist($id);
        $userPermission = SocialMediaPermission::findOne(['publisher_id' => $id]);
        $formModifyAccount = new ModifyAccountForm();
        $formSocialMediaPermission = new ModifySocialMediaPermissionForm();
        $canEditAllInputs = true;

        if($user->role === \app\models\User::USER_ROLE_ADMIN && $user->id === MainController::getCurrentUser()->id) {
            $canEditAllInputs = false;
        }

        if ($formModifyAccount->load($_POST) && $formModifyAccount->validate()) {
            $this->checkIfAdminCanEditAllInputs($user, $canEditAllInputs);
            $this->updateAccount($formModifyAccount, $user);
        }

        if ($formSocialMediaPermission->load($_POST) && $formSocialMediaPermission->validate()) {

            if ($userPermission === null) {
                $this->createSocialMediaPermission($formSocialMediaPermission, $user);
            } else {
                $this->updateSocialMediaPermission($formSocialMediaPermission, $userPermission);
            }

        }

        $formModifyAccount->firstname    = $user->firstname;
        $formModifyAccount->lastname     = $user->lastname;
        $formModifyAccount->email        = $user->email;
        $formModifyAccount->role         = $user->role;
        $formModifyAccount->is_active    = $user->is_active;
        $formSocialMediaPermission->facebook_enabled = $userPermission->facebook_enabled;
        $formSocialMediaPermission->twitter_enabled  = $userPermission->twitter_enabled;
        $formSocialMediaPermission->linkedin_enabled = $userPermission->linkedin_enabled;

        return $this->render('modify-account', [
            'formModifyAccountModel' => $formModifyAccount,
            'formSocialMediaPermissionModel' => $formSocialMediaPermission,
            'user' => $user,
            'canEditAllInputs' => $canEditAllInputs
        ]);
    }

    private function checkIfAdminCanEditAllInputs(User $userModified, $canEditAllInputs)
    {
        $unauthorizedException = NotFoundHttpException::class;

        if($canEditAllInputs) {
            return;
        }

        if ($userModified->id === MainController::getCurrentUser()->id) {
            throw new $unauthorizedException();
        }

    }

    /**
     * Try to save datas from form in the database
     *
     * @param ModifyAccountForm $form
     * @param $user
     * @throws CannotSaveException
     */
    private function updateAccount(ModifyAccountForm $form, User $user)
    {
        $user->firstname    = $form->firstname;
        $user->lastname     = $form->lastname;
        $user->email        = $form->email;
        $user->role         = $form->role;
        $user->is_active    = $form->is_active;

        if (!$user->save()) {
            throw new CannotSaveException($user);
        }
    }

    /**
     * Create a new publisher permission 
     *
     * @param ModifySocialMediaPermissionForm $formSocialMediaPermission
     * @param $user
     * @throws CannotSaveException
     */
    private function createSocialMediaPermission(ModifySocialMediaPermissionForm $formSocialMediaPermission, $user)
    {
        $userPermission = new SocialMediaPermission();

        $userPermission->publisher_id     = $user->id;
        $userPermission->facebook_enabled = $formSocialMediaPermission->facebook_enabled;
        $userPermission->twitter_enabled  = $formSocialMediaPermission->twitter_enabled;
        $userPermission->linkedin_enabled = $formSocialMediaPermission->linkedin_enabled;

        if (!$userPermission->save()) {
            throw new CannotSaveException($userPermission);
        }
    }

    /**
     * @param ModifySocialMediaPermissionForm $formSocialMediaPermission
     * @param $userPermission
     * @throws CannotSaveException
     */
    private function updateSocialMediaPermission(ModifySocialMediaPermissionForm $formSocialMediaPermission, SocialMediaPermission $userPermission)
    {
        $userPermission->facebook_enabled = $formSocialMediaPermission->facebook_enabled;
        $userPermission->twitter_enabled  = $formSocialMediaPermission->twitter_enabled;
        $userPermission->linkedin_enabled = $formSocialMediaPermission->linkedin_enabled;

        if (!$userPermission->save()) {
            throw new CannotSaveException($userPermission);
        }
    }

    /**
     * Check if the user given in the URL exist in the database
     *
     * @param int $id
     * @return User|null
     */
    private function checkIfUserExist($id)
    {
        $notFoundException = NotFoundHttpException::class;
        if (!is_null($user = User::findOne(['id' => $id]))) {
            return $user;
        }
        throw new $notFoundException();
    }

    /**
     * Display a page with the acount creation form
     * Test the form and if it's validated, calls the function createAccount
     *
     * @return string
     * @throws CannotCreateTokenException
     * @throws CannotSaveException
     * @throws Exception
     */
    public function actionCreateAccount()
    {
        $form = new CreateAccountForm();

        if($form->load($_POST) && $form->validate()) {
            $this->createAccount($form);
        }
        return $this->render('create-account', [
            'model'=>$form,
        ]);
    }

    /**
     * Create a new user with the datas from the form
     *
     * @param CreateAccountForm $form
     * @throws CannotSaveException
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws CannotCreateTokenException
     * @throws \Exception
     */
    private function createAccount(CreateAccountForm $form)
    {
        $user = new User();

        $user->firstname = $form->firstname;
        $user->lastname = $form->lastname;
        $user->email = $form->email;
        $user->is_validated = false;
        $user->is_active = true;
        $user->role = $form->role;
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
        $this->mailToUserPasswordCreation($user);
        $this->redirect("/management/accounts/" . $user->id);

    }

    /**
     * Platforrm settings page.
     * Display a form to change te proposal
     * approvement settings and display the
     * social media installed on the platform.
     *
     * @return string
     * @throws CannotSaveException
     */
    public function actionPlatformSettings()
    {

        $mainApprovementSettings = ProposalApprovementSetting::findOne([
            'id' => ProposalApprovementSetting::MAIN_SETTING]);
        $proposalApprovementSettingFormModel = new ProposalApprovementSettingForm();

        if ($proposalApprovementSettingFormModel->load(\Yii::$app->request->post())
            && $proposalApprovementSettingFormModel->validate()) {
            $mainApprovementSettings->approvement_percent = $proposalApprovementSettingFormModel->approvement_percent;
            $mainApprovementSettings->required_review = $proposalApprovementSettingFormModel->required_review;

            if (!$mainApprovementSettings->save()) {
                throw new CannotSaveException($mainApprovementSettings);
            }
        }

        $proposalApprovementSettingFormModel->required_review = $mainApprovementSettings->required_review;
        $proposalApprovementSettingFormModel->approvement_percent = $mainApprovementSettings->approvement_percent;
        $socialMediasDataProvider = new ActiveDataProvider([
            'query' => EnabledSocialMedia::find(),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $this->render('platform-settings', [
            'socialMediasDataProvider' => $socialMediasDataProvider,
            'proposalApprovementSettingFormModel' => $proposalApprovementSettingFormModel
        ]);
    }

    /**
     * Enable or disable social media in Ajax call.
     *
     * @return string
     * @throws CannotSaveException
     */
    public function actionEnableSocialMedia()
    {
        $unauthorizedException = NotFoundHttpException::class;

        if (\Yii::$app->request->post()) {
            $socialMedia = $this->checkIfSocialMediaExists(\Yii::$app->request->post()['social_media_name']);

            if (\Yii::$app->request->post()['enabled'] === 'true') {
                $socialMedia->is_enabled = 1;
            } else {
                $socialMedia->is_enabled = 0;
            }

            if (!$socialMedia->save()) {
                throw new CannotSaveException($socialMedia);
            }

            return \Yii::$app->response->statusCode;
        }

        throw new $unauthorizedException();
    }

    /**
     * Check if the social media exists
     *
     * @param string $id
     * @return EnabledSocialMedia|null
     */
    private function checkIfSocialMediaExists(string $id)
    {
        $notFoundException = NotFoundHttpException::class;
        if (!is_null($socialMedia = EnabledSocialMedia::findOne(['social_media_name' => $id]))) {
            return $socialMedia;
        }
        throw new $notFoundException();
    }

    /**
     * @param User $user
     * @throws CannotSendMailException
     * @throws Exception
     */
    private function mailToUserPasswordCreation(User $user)
    {
        $mail = Util::getConfiguredMailerForMailhog();
        $mail->addAddress($user->email);
        $mail->isHTML(false);
        $mail->CharSet = 'UTF-8';

        $mail->Subject = "Activate your Collab'media account";
        $mail->Body = 'Click on the following link to create your password : '. Util::BASE_URL .'/site/validate-account/' . $user->token ;

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

    /**
     * @param null $id
     * @return \yii\web\Response
     * @throws CannotCreateTokenException
     * @throws CannotSaveException
     * @throws MethodNotAllowedHttpException
     */
    public function actionResetPassword($id = null)
    {
        if (\Yii::$app->request->isPost === false) {
            throw new MethodNotAllowedHttpException;
        }
        $user = $this->checkIfUserExist($id);

        $this->resetPassword($user);

        $redirect = $_POST["redirect"] ?? "/management/accounts";

        return $this->redirect($redirect);
    }

    /**
     * @param $user
     * @throws CannotCreateTokenException
     * @throws CannotSaveException
     * @throws \Exception
     */
    private function resetPassword($user)
    {
        $user->password_hash = null;
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
        $this->mailToUserResetPassword($user);
    }

    /**
     * @param User $user
     * @return bool
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws CannotSendMailException
     */
    private function mailToUserResetPassword(User $user)
    {
        $mail = Util::getConfiguredMailerForMailhog();
        $mail->addAddress($user->email);
        $mail->isHTML(false);
        $mail->CharSet = 'UTF-8';

        $mail->Subject = "We have reset your Collab'media account password";
        $mail->Body = 'Click on the following link to create a new password : '. Util::BASE_URL .'/site/change-password/' . $user->token ;

        if(!$mail->send()) {
            throw new CannotSendMailException();
        }
    }
}
