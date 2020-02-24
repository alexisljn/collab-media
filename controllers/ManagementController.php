<?php
namespace app\controllers;

use app\controllers\mainController\MainController;
use app\models\databaseModels\User;
use Yii;
use yii\data\ActiveDataProvider;
use app\models\ModifyAccountForm;


class ManagementController extends MainController
{
    /**
     * Display a page where the accounts are shown
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
     * $id is an id from a User
     */
    private function actionModifiyAccount($id)
    {
        $user =  User::findOne(['id' => $id]);

        $form = new ModifyAccountForm();

        /**
         * this if populate $form with the datas from $_POST
         * and check if it respect the model from ModifyAccountForm
         * Then calls to function aupdateAccount
         */
        if($form->load($_POST) && $form->validate()) {
            $this->updateAccount($form, $user);
        }

        /**
         * Affect to the form the value we have in the user object
         */
        $form->firstname    = $user->firstname;
        $form->lastname     = $user->lastname;
        $form->email        = $user->email;
        $form->is_validated = $user->is_validated;
        $form->is_active    = $user->is_active;


        /**
         * Return the form so that we can use it in the view modifyAccount.php
         */
        return $this->render('modifyAccount', [
            'model'=>$form,
        ]);
    }

    private function updateAccount(ModifyAccountForm $form, $user)
    {
        /**
         * Populate the user object with the datas from the form
         */
        $user->firstname    = $form->firstname;
        $user->lastname     = $form->lastname;
        $user->email        = $form->email;
        $user->is_validated = $form->is_validated;
        $user->is_active    = $form->is_active;

        /**
         * Try to save it in the database
         * If the condition equals true, then the normal page will be displayed witht the changes done
         * If the condition equals false, then we echo the strnig save failed
         */
        if($user->save()){


        } else {
            echo "save failed.";
        }

    }
}
?>