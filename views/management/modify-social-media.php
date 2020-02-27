<?php
/* @var $formModifySocialMediasModel app\models\forms\ModifySocialMediaInformationsForm */
/* @var $socialMedia \app\models\databaseModels\EnabledSocialMedia */


use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Modify Social Media';
?>

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo "<h1 class='social-media-name'>" . $socialMedia->social_media_name . "</h1>";?>

    <?php $formModifySocialMedia = ActiveForm::begin([
        'id' => 'modify-social-media-form',
        'method' => 'post',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
    ]);?>

    <?= $formModifySocialMedia->field($formModifySocialMediasModel, 'is_enabled')->checkbox() ?>

    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?= Html::submitButton('Modify Account', [
                'class' => 'btn btn-primary', 'name' => 'modify-account-button'
            ]) ?>
        </div>
    </div>


    <?php ActiveForm::end();
