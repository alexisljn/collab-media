<?php
/** @var \app\models\databaseModels\Proposal $selectedProposal */
/** @var \app\models\databaseModels\ProposalContentHistory $lastProposalContent */
/** @var \app\models\databaseModels\Review|\app\models\databaseModels\Comment|\app\models\databaseModels\ProposalContentHistory $chronologicalStream */
?>

<!-- Proposal informations -->

<h1><?= \yii\helpers\Html::encode($selectedProposal->title) ?></h1>
<p>Created at <?= $selectedProposal->date ?></p>
<?php if($selectedProposal->date != $lastProposalContent->date) { ?>
    <p>Last edit : <?= $lastProposalContent->date ?></p>
<?php } ?>
<p>Status : <?= $selectedProposal->status ?></p>
<?php if (!is_null($selectedProposal->social_media)) { ?>
    <p>Published on : <?= $selectedProposal->social ?></p>
<?php } ?>
<p><?= (new Parsedown())
        ->text(\yii\helpers\Html::encode($lastProposalContent->content)) ?></p>

<!-- Proposal History -->

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
            <div id="viewProposition"></div>
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
}
?>