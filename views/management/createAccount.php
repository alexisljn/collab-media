<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\CreateAccountForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Create Account';
?>
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin([
        'id' => 'createAccount-form',
        'method' => 'post',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ]
    ]);?>

    <?= $form->field($model, 'firstname')?>

    <?= $form->field($model, 'lastname')?>

    <?= $form->field($model, 'email')?>

    <?= $form->field($model, 'role')->dropDownList(
        ['user' => 'User', 'reviewer' => 'Reviewer', 'publisher' => 'Publisher', 'admin' => 'Admin']
    )?>

    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'create-account-button']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>