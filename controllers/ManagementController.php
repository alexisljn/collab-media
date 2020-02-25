<?php
namespace app\controllers;

use app\controllers\mainController\MainController;
use app\models\databaseModels\User;
use yii\data\ActiveDataProvider;
use app\models\ModifyAccountForm;
use yii\web\NotFoundHttpException;


class ManagementController extends MainController
{
    /**
     * Action that allows an admin to look at all the accounts
     *
     * @param null | User $id
     * @return string
     */
    public function actionAccounts(User $id = null)
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
     * @param User $id
     * @return string
     */
    private function actionModifiyAccount(User $id)
    {

        $user = $this->checkIfUserExist($id);

        $form = new ModifyAccountForm();

        if($form->load($_POST) && $form->validate()) {
            $this->updateAccount($form, $user);
        }

        $form->firstname    = $user->firstname;
        $form->lastname     = $user->lastname;
        $form->email        = $user->email;
        $form->is_validated = $user->is_validated;
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
     */
    private function updateAccount(ModifyAccountForm $form, $user)
    {

        $user->firstname    = $form->firstname;
        $user->lastname     = $form->lastname;
        $user->email        = $form->email;
        $user->is_validated = $form->is_validated;
        $user->is_active    = $form->is_active;

        if(!$user->save()){
            echo "save failed.";
        }
    }

    /**
     * Check if the user given in the URL exist in the database
     *
     * @param User $id
     * @return User|null
     */
    private function checkIfUserExist(User $id)
    {
        $unauthorizedException = NotFoundHttpException::class;
        if (!is_null($user = User::findOne(['id' => $id]))) {
            return $user;
        }
        throw new $unauthorizedException();
    }
}
?>