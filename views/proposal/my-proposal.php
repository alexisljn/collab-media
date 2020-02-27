<?php
/** @var \app\models\databaseModels\Proposal $selectedProposal */
/** @var \app\models\databaseModels\ProposalContentHistory $lastProposalContent */
/** @var \app\models\databaseModels\Review|\app\models\databaseModels\Comment|\app\models\databaseModels\ProposalContentHistory $chronologicalStream */

use yii\helpers\Html;
use yii\widgets\ActiveForm; ?>

<!-- Proposal informations -->

<a href="#" id="edit-link" class="content-layout">Edit</a>
<h1 class="content-layout"><?= \yii\helpers\Html::encode($selectedProposal->title) ?></h1>
<p class="content-layout">Created at <?= $selectedProposal->date ?></p>
<?php if($selectedProposal->date != $lastProposalContent->date) { ?>
    <p class="content-layout">Last edit : <?= $lastProposalContent->date ?></p>
<?php } ?>
<p class="content-layout">Status : <?= $selectedProposal->status ?></p>
<?php if (!is_null($selectedProposal->social_media)) { ?>
    <p class="content-layout">Published on : <?= $selectedProposal->social ?></p>
<?php } ?>
<div class="content-layout" id="original-content"><?= (new Parsedown())
        ->text(\yii\helpers\Html::encode($lastProposalContent->content)) ?></div>

<!-- Proposal History -->

<!-- Edit Form -->
<div style="display: none;" class="form-layout">
    <?php
    $form = yii\widgets\ActiveForm::begin([
        'id' => 'proposal-form',
        'action' => '/proposal/edit-proposal/'. $selectedProposal->id
    ]);
    ?>
    <?= $form->field($model, 'title')->textInput(['id' => 'proposal-form-title-input']); ?>
    <?= $form->field($model, 'content')->hiddenInput(['id' => 'proposal-form-content-input']); ?>
    <div id="proposal-content" class="edit-section"></div>
    <?= $form->field($model, 'relatedFile')->fileInput(); ?>
    <?= yii\helpers\Html::submitButton('Edit'); ?>
    <?php yii\widgets\ActiveForm::end(); ?>
</div>

<?php foreach ($chronologicalStream as $oldProposalContent) {
    if ($oldProposalContent instanceof \app\models\databaseModels\ProposalContentHistory && $oldProposalContent->date != $lastProposalContent->date) { ?>
        <div class="bg-success">
            <p>Previous version  of <?=' '. $oldProposalContent->date ?></p>
            <p><?= (new Parsedown())
                    ->text(\yii\helpers\Html::encode($oldProposalContent->content)) ?></p>
        </div>
    <?php }
} ?>

<!-- Chronological Stream -->

<?php
foreach ($chronologicalStream as $chronologicalItem) {
    if($chronologicalItem instanceof \app\models\databaseModels\Comment) { ?>
        <div class="bg-primary">
            <p>
                <?= \yii\helpers\Html::encode($chronologicalItem->author->firstname) . ' ' .
                \yii\helpers\Html::encode($chronologicalItem->author->lastname) . ' - ' .
                $chronologicalItem->date ?>
            </p>
        </div>
    <?php }
    elseif ($chronologicalItem instanceof \app\models\databaseModels\Review) { ?>
        <div class="bg-secondary">
            <p>
                <?= \yii\helpers\Html::encode($chronologicalItem->reviewer->firstname) . ' ' .
                \yii\helpers\Html::encode($chronologicalItem->reviewer->lastname) . ' - ' .
                $chronologicalItem->date . ' ' .$chronologicalItem->status .' '?>this proposal.
            </p>
        </div>
    <?php }
    elseif ($chronologicalItem instanceof \app\models\databaseModels\ProposalContentHistory) {
        if ($chronologicalItem->date != $selectedProposal->date)
        { ?>
            <div class="bg-danger">
                <p>
                    <?= \yii\helpers\Html::encode($selectedProposal->submitter->firstname) . ' ' .
                    \yii\helpers\Html::encode($selectedProposal->submitter->lastname) .' ' ?>
                    edited this proposal on <?=' ' . $chronologicalItem->date ?>
                </p>
            </div>
    <?php }
     }
    elseif ($chronologicalItem instanceof \app\models\databaseModels\ProposalFileHistory) {
        if ($chronologicalItem->date != $selectedProposal->date) { ?>
        <div class="bg-warning">
            <p>
                <?= \yii\helpers\Html::encode($selectedProposal->submitter->firstname) . ' ' .
                \yii\helpers\Html::encode($selectedProposal->submitter->lastname) .' ' ?>
                uploaded new file <?= $chronologicalItem->path ?> the <?=' ' . $chronologicalItem->date ?>
            </p>
        </div>
    <?php
        }
    }
}
?>
<script type="text/javascript">
    $(() => {
        const form = document.querySelector('#proposal-form');
        const editor = new tui.Editor({
            el: document.querySelector('.edit-section'),
            previewStyle: 'vertical',
            height: '300px',
            initialEditType: 'markdown',
            initialValue: `<?= $lastProposalContent->content ?>`
        });
        $('#edit-link').on('click', () => {
            $('.form-layout').css('display', 'block');
            $('.content-layout').css('display', 'none');
            editor.setMarkdown(`<?= $lastProposalContent->content ?>`);
        });
        $(form).on("submit", function() {
            $("#proposal-form-content-input").val(editor.getMarkdown());
        })
    });
</script>
