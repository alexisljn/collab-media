<?php
/* @var $this yii\web\View */
/* @var  $forgottenPasswordModel app\models\forms\ForgottenPasswordForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
$this->title = 'Forgotten Password';
?>
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $formForgottenPassword = ActiveForm::begin([
        'id' => 'forgotten-password-form',
        'method' => 'post',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
    ]);
    ?>

    <?= $formForgottenPassword->field($forgottenPasswordModel, 'email')?>

    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?= Html::submitButton('Submit', [
                'class' => 'btn btn-primary', 'name' => 'forgotten-password-button'
            ]) ?>
        </div>
    </div>

    <?php ActiveForm::end();?>
