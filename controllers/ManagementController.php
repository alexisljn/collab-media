<?php
namespace app\controllers;

use app\controllers\mainController\MainController;
use app\models\CreateAccountForm;
use app\models\databaseModels\User;
use app\models\exceptions\CannotSaveException;
use yii\data\ActiveDataProvider;
use app\models\ModifyAccountForm;
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

        $form = new ModifyAccountForm();

        if($form->load($_POST) && $form->validate()) {
            $this->updateAccount($form, $user);
        }

        $form->firstname    = $user->firstname;
        $form->lastname     = $user->lastname;
        $form->email        = $user->email;
        $form->role         = $user->role;
        $form->is_active    = $user->is_active;

        return $this->render('modifyAccount', [
            'model'=>$form,
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

        if(!$user->save()){
            throw new CannotSaveException($user);
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