<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $formModifyAccount_model app\models\forms\ModifyAccountForm */
/* @var $formSocialMediaPermission_model app\models\forms\ModifySocialMediaPermissionForm */
/* @var $user \app\models\databaseModels\User */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Modify Account';
?>
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $formModifyAccount = ActiveForm::begin([
        'id' => 'modifyAccount-form',
        'method' => 'post',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
    ]);?>

    <?= $formModifyAccount->field($formModifyAccount_model, 'firstname')?>

    <?= $formModifyAccount->field($formModifyAccount_model, 'lastname')?>

    <?= $formModifyAccount->field($formModifyAccount_model, 'email')?>

    <?= $formModifyAccount->field($formModifyAccount_model, 'role')->dropDownList([
            'user'=>'User', 'reviewer'=>'Reviewer','publisher'=>'Publisher','admin'=>'Admin'
    ],[
        'options'=>[
            'role' => ['selected' => true]
        ]]) ?>

    <?= $formModifyAccount->field($formModifyAccount_model,'is_active')->checkbox(); ?>

    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?= Html::submitButton('Modify Account', [
                    'class' => 'btn btn-primary', 'name' => 'modify-account-button'
            ]) ?>
        </div>
    </div>


    <?php ActiveForm::end();

    if (in_array($formModifyAccount_model->role, ['publisher','admin'])) {
        $formModifySocialMediaPermission = ActiveForm::begin([
            'id' => 'modifySocialMedia-form',
            'method' => 'post',
            'layout' => 'horizontal',
            'fieldConfig' => [
                'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
                'labelOptions' => ['class' => 'col-lg-1 control-label'],
            ],
        ]);?>

        <?= $formModifySocialMediaPermission->field($formSocialMediaPermission_model,'facebook_enabled')->checkbox(); ?>

        <?= $formModifySocialMediaPermission->field($formSocialMediaPermission_model,'twitter_enabled')->checkbox(); ?>

        <?= $formModifySocialMediaPermission->field($formSocialMediaPermission_model,'linkedin_enabled')->checkbox(); ?>

        <div class="form-group">
            <div class="col-lg-offset-1 col-lg-11">
                <?= Html::submitButton('Modify Permission', [
                        'class' => 'btn btn-primary', 'name' => 'modify-social-media-permission-button'
                ]) ?>
            </div>
        </div>
    <?php ActiveForm::end();
    } ?>



