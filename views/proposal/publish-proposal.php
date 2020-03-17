<?php
/** @var \app\models\forms\PublishProposalForm $publishProposalFormModel */
/** @var string $lastContent */
/** @var int $proposalId  */

use yii\helpers\Html; ?>
<h1>Publish proposal</h1>
<p>Handle layout</p>
<?php $form = yii\widgets\ActiveForm::begin([
                'id' => 'publishg-form',
                'action' => '/proposal/publish-proposal/'. $proposalId]); ?>
<span id="alert"></span>
<?= $form->field($publishProposalFormModel, 'content')->textarea(['id' => 'publish-form-content-input', 'rows' => '8',]); ?>
<?= $form->field($publishProposalFormModel, 'content')->hiddenInput(['id' => 'publish-form-file-input'])->label(false); ?>
<?= yii\helpers\Html::submitButton('Publish', ['class' => 'btn btn-not-outline']); ?>
<?php yii\widgets\ActiveForm::end(); ?>

<script type="text/javascript">
    $(() => {

    });
        /*let content = $('#publish-form-content-input');
      // content.val(`<?= $lastContent ?>`);
        //console.log($('#publish-form-content-input').val());

       /* if(content.text().length > <?= \app\models\forms\PublishProposalForm::TWEET_MAX_CHARS ?>) {
            console.log('totototot');
        }

        /*content.on('input', (e) => {
            console.log($('#publish-form-content-input').text());
            /*if (content.text().length > <?= \app\models\forms\PublishProposalForm::TWEET_MAX_CHARS ?>) {
                console.log("red border")
            } else {
                console.log('noBorder')
            }
            console.log(e.target);
        })

        $('#proposal-form').on('submit', () => {
            console.log("teeeee");
        })
</script>