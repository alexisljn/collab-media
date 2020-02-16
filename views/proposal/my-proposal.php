<?php
/** @var \app\models\databaseModels\Proposal $selectedProposal */
/** @var \app\models\databaseModels\ProposalContentHistory $lastProposalContent */
/** @var \app\models\databaseModels\Review|\app\models\databaseModels\Comment|\app\models\databaseModels\ProposalContentHistory $chronologicalStream */
?>

<!-- Proposal informations -->

<h1><?= $selectedProposal->title ?></h1>
<p>Created at <?= $selectedProposal->date ?></p>
<?php if($selectedProposal->date != $lastProposalContent->date) { ?>
    <p>Last edit : <?= $lastProposalContent->date ?></p>
<?php } ?>
<p>Status : <?= $selectedProposal->status ?></p>
<?php if (!is_null($selectedProposal->social_media)) { ?>
    <p>Published on : <?= $selectedProposal->social ?></p>
<?php } ?>
<p><?= $lastProposalContent->content ?></p>

<!-- Proposal History -->

<?php foreach ($chronologicalStream as $oldProposalContent) {
    if ($oldProposalContent instanceof \app\models\databaseModels\ProposalContentHistory && $oldProposalContent->date != $lastProposalContent->date) { ?>
        <div class="bg-success">
            <p>Previous version  of <?=' '. $oldProposalContent->date ?></p>
            <p><?= $oldProposalContent->content ?></p>
        </div>
    <?php }
} ?>

<!-- Chronological Stream -->

<?php
foreach ($chronologicalStream as $chronologicalItem) {
    if($chronologicalItem instanceof \app\models\databaseModels\Comment) { ?>
        <div class="bg-primary">
            <p><?= $chronologicalItem->author->firstname . ' ' . $chronologicalItem->author->lastname . ' - ' . $chronologicalItem->date ?></p>
            <p><?= $chronologicalItem->content ?></p>
        </div>
    <?php }
    elseif ($chronologicalItem instanceof \app\models\databaseModels\Review) { ?>
        <div class="bg-secondary">
            <p>
                <?= $chronologicalItem->reviewer->firstname . ' ' . $chronologicalItem->reviewer->lastname . ' - ' . $chronologicalItem->date
                . ' ' .$chronologicalItem->status .' '?>this proposal.
            </p>
        </div>
    <?php }
    elseif ($chronologicalItem instanceof \app\models\databaseModels\ProposalContentHistory) {
        if ($chronologicalItem->date != $selectedProposal->date)
        { ?>
            <div class="bg-danger">
                <p>
                    <?= $selectedProposal->submitter->firstname . ' ' . $selectedProposal->submitter->lastname .' ' ?> edited this proposal on
                    <?=' ' . $chronologicalItem->date ?>
                </p>
            </div>
    <?php }
     }
}
?>