<?php
namespace app\controllers;

use app\controllers\mainController\MainController;
use app\models\databaseModels\SocialMediaPermission;
use app\models\forms\CreateAccountForm;
use app\models\databaseModels\User;
use app\models\exceptions\CannotSaveException;
use app\models\forms\ModifySocialMediaPermissionForm;
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
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
        return $this->render('accounts', [
            'usersDataProvider' => $usersDataProvider
        ]);
    }

    /**
     * Display a page where the account of the user selected in actionAccounts
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

        return $this->render('modifyAccount', [
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
        $unauthorizedException = NotFoundHttpException::class;
        if (!is_null($user = User::findOne(['id' => $id]))) {
            return $user;
        }
        throw new $unauthorizedException();
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
        return $this->render('createAccount', [
            'model'=>$form,
        ]);
    }

    /**
     * Create a new user with the datas from the form
     *
     * @param CreateAccountForm $form
     * @throws CannotSaveException
     */
    private function createAccount(CreateAccountForm $form)
    {
        $user = new User();

        $user->firstname    = $form->firstname;
        $user->lastname     = $form->lastname;
        $user->email        = $form->email;
        $user->is_validated = false;
        $user->is_active = true;
        $user->role         = $form->role;

        if(!$user->save()){
            throw new CannotSaveException($user);
        }
    }
}
?>