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
    {   $user =  User::findOne(['id' => $id]);
        //dd($user);

        $form = new ModifyAccountForm();
        $form->firstname    = $user->firstname;
        $form->lastname     = $user->lastname;
        $form->email        = $user->email;
        $form->is_validated = $user->is_validated;
        $form->is_active    = $user->is_active;

        if(isset($_POST['ModifyAccountForm'])) {
            $form->attributes = $_POST['ModifyAccountForm'];
        }
        return $this->render('modifyAccount', [
            'model'=>$form,
            'user' => $user,
        ]);
    }
}
