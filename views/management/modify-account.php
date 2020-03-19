<?php

/** @var $this yii\web\View */
/** @var $formModifyAccountModel app\models\forms\ModifyAccountForm */
/** @var $formSocialMediaPermissionModel app\models\forms\ModifySocialMediaPermissionForm */
/** @var $user app\models\databaseModels\User */
/** @var bool $canEditAllInputs */

use app\models\User;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Modify Account';
?>
<div class="row mb-5">
    <div class="col-12">
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

        <?= $formModifyAccount->field($formModifyAccountModel, 'role')
            ->dropDownList([
                User::USER_ROLE_MEMBER => 'Member',
                User::USER_ROLE_REVIEWER => 'Reviewer',
                User::USER_ROLE_PUBLISHER => 'Publisher',
                User::USER_ROLE_ADMIN => 'Admin'
            ],
            [ 'options'=> [
                    'role' => ['selected' => true],
                ]
            ])?>

        <?= $formModifyAccount->field($formModifyAccountModel,'is_active')->checkbox(); ?>

        <div class="form-group">
            <div class="col-lg-offset-1 col-lg-11">
                <?= Html::submitButton('Modify Account', [
                    'class' => 'btn btn-primary', 'name' => 'modify-account-button'
                ]) ?>
            </div>
        </div>

        <?php ActiveForm::end();?>
    </div>
</div>


<?php
if (in_array($formModifyAccountModel->role, [User::USER_ROLE_PUBLISHER, User::USER_ROLE_ADMIN])) {
    ?>
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="h5">Publisher permissions</h2>
            <p>Choose on which social network this user can publish content</p>
            <?php
            $formModifySocialMediaPermission = ActiveForm::begin([
                'id' => 'modify-social-media-form',
                'method' => 'post',
                'layout' => 'horizontal',
                'fieldConfig' => [
                    'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
                    'labelOptions' => ['class' => 'col-lg-1 control-label'],
                ],
            ]);?>
            <?= $formModifySocialMediaPermission->field($formSocialMediaPermissionModel,'facebook_enabled')->checkbox()->label('Facebook'); ?>
            <?= $formModifySocialMediaPermission->field($formSocialMediaPermissionModel,'twitter_enabled')->checkbox()->label('Twitter'); ?>
            <?= $formModifySocialMediaPermission->field($formSocialMediaPermissionModel,'linkedin_enabled')->checkbox()->label('Linkedin'); ?>
            <div class="form-group">
                <div class="col-lg-offset-1 col-lg-11">
                    <?= Html::submitButton('Modify Permissions', [
                        'class' => 'btn btn-primary', 'name' => 'modify-social-media-permission-button'
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
<?php ActiveForm::end();
} ?>
<div class="row mb-5">
    <div class="col-12">
        <h2 class="h5">Reset User Password</h2>
        <p>You can reset the user password by clicking on the button below. He will receive an email to define a new password.</p>
        <form method="post" action="/management/reset-password/<?= $user->id; ?>" onsubmit="return confirm('Are you sure you want to reset the password of this user?')">
            <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
            <input hidden name="redirect" value="/management/accounts/<?= $user->id; ?>">
            <div class="form-group">
                <div class="col-lg-offset-1 col-lg-11">
                    <button type="submit" class="btn btn-warning">Reset Password</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript" id="modify-account-script">
    $(() => {
        if ('<?= $canEditAllInputs ?>' === '') {
            $('#modifyaccountform-role').attr('disabled', 'true');
            $('#modifyaccountform-is_active').attr('disabled', 'true');
        }
    });
</script>