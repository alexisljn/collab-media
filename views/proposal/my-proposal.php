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

<!-- Chronological Stream -->

