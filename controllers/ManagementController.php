<?php
namespace app\controllers;

use app\controllers\mainController\MainController;
use app\models\databaseModels\User;
use Yii;
use yii\data\ActiveDataProvider;


class ManagementController extends MainController
{
    /**
     * Display a page where the accounts are shown
     */
    public function actionAccounts()
    {


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
}
