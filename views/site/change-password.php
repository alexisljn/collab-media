<?php

/* @var $this yii\web\View */
/* @var  $model app\models\forms\ChangePasswordForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Change Password';
?>
    <h1><?= Html::encode($this->title) ?></h1>

<?php $formChangePassword = ActiveForm::begin([
    'id' => 'validate-account-form',
    'method' => 'post',
    'layout' => 'horizontal',
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
        'labelOptions' => ['class' => 'col-lg-1 control-label'],
    ],
]);
?>

<?= $formChangePassword->field($model, 'password')?>

<?= $formChangePassword->field($model, 'confirmPassword')?>

    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?= Html::submitButton('Validate Password', [
                'class' => 'btn btn-primary', 'name' => 'validate-account-button'
            ]) ?>
        </div>
    </div>

<?php ActiveForm::end();?>