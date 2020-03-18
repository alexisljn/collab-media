<?php
/** @var \app\models\forms\PublishProposalForm $publishProposalFormModel */
/** @var string $lastContent */
/** @var \app\models\Proposal $selectedProposal  */
/** @var array $allowedSocialMedia */

use yii\helpers\Html; ?>

<div class="row">
    <div class="col-12">
        <h1><?= Html::encode($selectedProposal->title) ?></h1>
    </div>
</div>
<?php if (!is_null($selectedProposal->file)) { ?>
    <div id="#proposal-file" class="text-center proposal-timeline-text-element-content">
        <?php if (explode('.', $selectedProposal->file->path)[1] !== 'mp4') { ?>
            <img src="/proposal/get-file/<?= $selectedProposal->id ?>"  class="img-fluid" alt="proposal file">
        <?php } else { ?>
            <video controls width="1280" class="img-fluid">
                <source src="/proposal/get-file/<?= $selectedProposal->id ?>" type="video/mp4">
            </video>
        <?php } ?>
    </div>
<?php } ?>
<?php $form = yii\widgets\ActiveForm::begin([
                'id' => 'publishing-form',
                'action' => '/proposal/publish-proposal/'. $selectedProposal->id]); ?>
<?= $form->field($publishProposalFormModel, 'content')->textarea(['id' => 'publish-form-content-input', 'rows' => '8',])->label('Publication body'); ?>
<span id="alert" class="text-danger"></span>
<?php if (!is_null($selectedProposal->file)) { ?>
<?= $form->field($publishProposalFormModel, 'file')->checkbox(['label' => 'Publish file', 'id' => 'publish-file']); ?>
<?php } else { ?>
     <?= $form->field($publishProposalFormModel, 'file')->hiddenInput(['value' => 0])->label(false); ?>
<?php } ?>
<?= $form->field($publishProposalFormModel, 'social_media')->checkboxList($allowedSocialMedia,
    ['item' => function($index, $label, $name, $checked, $value){
        return Html::checkbox($name, $checked, [
            'value' => $value,
            'label' => $label,
            'id' => $value . '-checkbox',
        ]);
    }]) ?>
<?= yii\helpers\Html::submitButton('Publish', ['id'=> 'submit-publish', 'class' => 'btn btn-not-outline']); ?>
<?php yii\widgets\ActiveForm::end(); ?>

<script type="text/javascript" id="submit-script">
    $(() => {
        let content = $('#publish-form-content-input');

        if(content.val().length > <?= \app\models\forms\PublishProposalForm::TWEET_MAX_CHARS ?>) {
            content.addClass('border border-danger');
            $('#alert').text('Content is too long');
            $('#submit-publish').attr('disabled', true);
        }

        $('#publish-file').attr('checked', true);

        $('#facebook-checkbox').attr('disabled', true);
        $('#linkedin-checkbox').attr('disabled', true);
        $('#twitter-checkbox').attr('checked', true);


        content.on('input', () => {
            if (content.val().length > <?= \app\models\forms\PublishProposalForm::TWEET_MAX_CHARS ?>) {
                content.addClass('border border-danger');
                $("#alert").text('Content is too long');
                $('#submit-publish').attr('disabled', true);
            } else {
                content.removeClass('border border-danger');
                $("#alert").text('');
                $('#submit-publish').attr('disabled', false);
            }
        });

    });
</script>