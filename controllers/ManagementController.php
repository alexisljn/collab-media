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

    private function actionModifiyAccount($id)
    {
        $user =  User::findOne(['id' => $id]);
        //dd($user);
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

    private function updateAccount(ModifyAccountForm $form, $user)
    {

        $user->firstname    = $form->firstname;
        $user->lastname     = $form->lastname;
        $user->email        = $form->email;
        $user->is_validated = $form->is_validated;
        $user->is_active    = $form->is_active;


        if($user->save()){
           

        } else {
            echo "save failed.";
        }

    }
}
