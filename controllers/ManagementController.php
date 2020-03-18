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
use PHPMailer\PHPMailer\Exception;
use yii\data\ActiveDataProvider;
use app\models\forms\ModifyAccountForm;
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
            return $this->actionModifiyAccount($id);
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
    private function actionModifiyAccount($id)
    {
        $user = $this->checkIfUserExist($id);

        $userPermission = SocialMediaPermission::findOne(['publisher_id' => $id]);


        $formModifyAccount = new ModifyAccountForm();

        $formSocialMediaPermission = new ModifySocialMediaPermissionForm();

        if ($formModifyAccount->load($_POST) && $formModifyAccount->validate()) {
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
        ]);
    }

    /**
     * Try to save datas from form in the database
     *
     * @param ModifyAccountForm $form
     * @param $user
     * @throws CannotSaveException
     */
    private function updateAccount(ModifyAccountForm $form, $user)
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
    private function updateSocialMediaPermission(ModifySocialMediaPermissionForm $formSocialMediaPermission, $userPermission)
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
     * @throws CannotSaveException
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
     * Action that allows an admin to look at all the social medias
     *
     * @param null $id
     * @return string
     * @throws CannotSaveException
     */
    public function actionSocialMedia($id = null)
    {
        if(!is_null($id)) {
           return $this->actionModifySocialMedia($id);
        }
        $socialMediasDataProvider = new ActiveDataProvider([
            'query' => EnabledSocialMedia::find(),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        return $this->render('social-media', [
            'socialMediasDataProvider' => $socialMediasDataProvider
        ]);
    }

    /**
     * Display a page where the social media selected in actionSocialMedias can be modified
     *
     * @param string $id
     * @return string
     * @throws CannotSaveException
     */
    private function actionModifySocialMedia(string $id)
    {
        $socialMedia = $this->checkIfSocialMediaExists($id);

        $formModifySocialMedias = new ModifySocialMediaInformationsForm();

        if ($formModifySocialMedias->load($_POST) && $formModifySocialMedias->validate()) {
            $this->updateSocialMedia($formModifySocialMedias, $socialMedia);
        }
        $formModifySocialMedias->is_enabled = $socialMedia->is_enabled;



        return $this->render('modify-social-media', [
            'formModifySocialMediasModel' => $formModifySocialMedias,
            'socialMedia' => $socialMedia
        ]);
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
     * Update the is_enabled value of a social media
     *
     * @param ModifySocialMediaInformationsForm $formModifySocialMedias
     * @param $socialMedia
     * @throws CannotSaveException
     */
    private function updateSocialMedia(ModifySocialMediaInformationsForm $formModifySocialMedias, $socialMedia)
    {
        $socialMedia->is_enabled = $formModifySocialMedias->is_enabled;

        if(!$socialMedia->save()){
            throw new CannotSaveException($socialMedia);
        }
    }

    /**
     * @param User $user
     * @return bool
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws CannotSendMailException
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
     * @throws CannotCreateTokenException
     * @throws CannotSaveException
     */
    public function actionResetPassword($id = null)
    {
        $user = $this->checkIfUserExist($id);

        $this->resetPassword($user);
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

        return $this->redirect("/management/accounts");
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
?>