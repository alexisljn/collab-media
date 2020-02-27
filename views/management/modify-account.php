<?php

/* @var $this yii\web\View */
/* @var $formModifyAccountModel app\models\forms\ModifyAccountForm */
/* @var $formSocialMediaPermissionModel app\models\forms\ModifySocialMediaPermissionForm */

use app\models\User;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Modify Account';
?>
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $formModifyAccount = ActiveForm::begin([
        'id' => 'modify-account-form',
        'method' => 'post',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
    ]);?>

    <?= $formModifyAccount->field($formModifyAccountModel, 'firstname')?>

    <?= $formModifyAccount->field($formModifyAccountModel, 'lastname')?>

    <?= $formModifyAccount->field($formModifyAccountModel, 'email')?>

    <?= $formModifyAccount->field($formModifyAccountModel, 'role')->dropDownList([
            User::USER_ROLE_MEMBER => 'Member',
            User::USER_ROLE_REVIEWER => 'Reviewer',
            User::USER_ROLE_PUBLISHER => 'Publisher',
            User::USER_ROLE_ADMIN => 'Admin'
    ],[
        'options'=>[
            'role' => ['selected' => true]
        ]]) ?>

    <?= $formModifyAccount->field($formModifyAccountModel,'is_active')->checkbox(); ?>

    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?= Html::submitButton('Modify Account', [
                    'class' => 'btn btn-primary', 'name' => 'modify-account-button'
            ]) ?>
        </div>
    </div>


    <?php ActiveForm::end();

    if (in_array($formModifyAccountModel->role, [User::USER_ROLE_PUBLISHER, User::USER_ROLE_ADMIN])) {
        $formModifySocialMediaPermission = ActiveForm::begin([
            'id' => 'modify-social-media-form',
            'method' => 'post',
            'layout' => 'horizontal',
            'fieldConfig' => [
                'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
                'labelOptions' => ['class' => 'col-lg-1 control-label'],
            ],
        ]);?>

        <?= $formModifySocialMediaPermission->field($formSocialMediaPermissionModel,'facebook_enabled')->checkbox(); ?>

        <?= $formModifySocialMediaPermission->field($formSocialMediaPermissionModel,'twitter_enabled')->checkbox(); ?>

        <?= $formModifySocialMediaPermission->field($formSocialMediaPermissionModel,'linkedin_enabled')->checkbox(); ?>

        <div class="form-group">
            <div class="col-lg-offset-1 col-lg-11">
                <?= Html::submitButton('Modify Permission', [
                        'class' => 'btn btn-primary', 'name' => 'modify-social-media-permission-button'
                ]) ?>
            </div>
        </div>
    <?php ActiveForm::end();
    } ?>



