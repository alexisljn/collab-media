<?php


namespace app\controllers\mainController;


use yii\web\Controller;

class MainController extends Controller
{
    public function beforeAction($action)
    {
        // TODO gestion des rôles en fonction de l'action appelée

        return parent::beforeAction($action);
    }
}