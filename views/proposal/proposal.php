<?php
/** @var \app\models\databaseModels\Proposal $selectedProposal */
/** @var \app\models\databaseModels\ProposalContentHistory $lastProposalContent */
/** @var \app\models\databaseModels\Review|\app\models\databaseModels\Comment|\app\models\databaseModels\ProposalContentHistory $chronologicalStream */
/** @var int $approvalsCount */
/** @var int $disapprovalsCount */

use app\models\Proposal;
use yii\helpers\Html;
use yii\widgets\ActiveForm; ?>

<div id="proposal-content-history-modal" class="modal-container">
    <div class="modal-content">
        <button id="proposal-content-history-button-close" class="modal-close-button"><i class="fas fa-times" style="font-size: 1.3em"></i></button>
        <h1>Editions history</h1>

        <?php foreach ($chronologicalStream as $oldProposalContent) {
            if ($oldProposalContent instanceof \app\models\databaseModels\ProposalContentHistory && $oldProposalContent->date != $lastProposalContent->date) { ?>
                <div class="bg-success">
                    <p>Previous version  of <?=' '. $oldProposalContent->date ?></p>
                    <p><?= (new Parsedown())
                            ->text(\yii\helpers\Html::encode($oldProposalContent->content)) ?></p>
                </div>
            <?php }
        } ?>
    </div>
</div>

<!-- Proposal informations -->
<div class="row">
    <div class="col-12">
        <h1><?= \yii\helpers\Html::encode($selectedProposal->title) ?></h1>
    </div>
</div>

<div class="row proposal-content">
    <div class="col-9 proposal-timeline">
        <!-- Proposal informations -->
        <?php if (!is_null($selectedProposal->social_media)) { ?>
            <p class="content-layout">Published on : <?= $selectedProposal->social ?></p>
        <?php } ?>
        <div class="proposal-timeline-text-element-container" id="proposal-content">
            <div class="proposal-timeline-text-element-content">
                <?= (new Parsedown())->text(\yii\helpers\Html::encode($lastProposalContent->content)) ?>
            </div>

            <?php
            $displayProposalContentFooter = false;

            if($selectedProposal->date !== $lastProposalContent->date) {
                $displayProposalContentFooter = true;
            }

            if($displayProposalContentFooter) {
                ?>
                <div class="proposal-timeline-text-element-footer">
                    <div style="float: right">Edited at <?= $lastProposalContent->date ?> – <a id="proposal-content-show-history-link" href="#">View history</a></div>
                    <div class="clear"></div>
                </div>
                <?php
            }
            ?>
        </div>

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
                    <p><?= \yii\helpers\Html::encode($chronologicalItem->content) ?></p>
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
    </div>
    <aside class="col-3 proposal-sidebar">
        <div class="proposal-sidebar-block">
<!--            TODO Display this block only if the current user is not the proposal submitter -->
            Created by <strong><?= Html::encode($selectedProposal->submitter->firstname . ' ' . $selectedProposal->submitter->lastname) ?></strong>
        </div>
        <div class="proposal-sidebar-divider"></div>
        <div class="proposal-sidebar-block">
<!--            TODO Display this block only if the current user is the proposal submitter AND if the proposal is still pending -->
            <button id="edit-link" class="btn btn-block btn-sm">Edit</button>
        </div>
        <div class="proposal-sidebar-divider"></div>
        <div class="proposal-sidebar-block">
            Status:
            <?php
            switch($selectedProposal->status) {
                case Proposal::STATUS_PENDING:
                    ?>
                    <span class="status pending">Pending</span>
                    <?php
                    break;
                case Proposal::STATUS_PUBLISHED:
                    ?>
                    <span class="status published">Published</span>
                    <?php
                    break;
                case Proposal::STATUS_REJECTED:
                    ?>
                    <span class="status rejected">Rejected</span>
                    <?php
                    break;
            }
            ?>
        </div>
        <div class="proposal-sidebar-block">
            <div>
                <span>Created at <?= $selectedProposal->date ?></span>
            </div>
            <?php
            if($selectedProposal->date !== $lastProposalContent->date) {
                ?>
                <span>Last edit at <?= $lastProposalContent->date ?></span>
                <?php
            }
            ?>
        </div>
        <div class="proposal-sidebar-divider"></div>
        <div class="proposal-sidebar-block">
            <div class="container no-padding">
                <div class="row">
                    <div class="col-lg-3 col-md-12">Rating:</div>
                    <div class="col-lg-9 col-md-12">
                        <div class="rating-viewer-container">
                            <div class="rating-viewer-counts-container">
                                <div class="rating-viewer-counts-approvals"><?= $approvalsCount ?></div>
                                <div class="rating-viewer-counts-disapprovals"><?= $disapprovalsCount ?></div>
                                <div class="clear"></div>
                            </div>
                            <div class="rating-viewer-bar-container">
                                <?php
                                $totalReviewsCount = $approvalsCount + $disapprovalsCount;
                                if($totalReviewsCount === 0) {
                                    $barPercentage = 50;
                                } else {
                                    $barPercentage = $approvalsCount / $totalReviewsCount * 100;
                                }
                                ?>
                                <div class="rating-viewer-approval-bar" style="width: <?= $barPercentage ?>%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </aside>
</div>

<script type="text/javascript" id="edit-form-script">
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


<script type="text/javascript" id="content-history-modal-script">
    $(() => {
        const modalContainer = $('#proposal-content-history-modal');
        const body = $(document.body);

        const displayModal = () => {
            modalContainer.addClass('visible');
            body.addClass('modal-open');
        };

        const hideModal = () => {
            modalContainer.removeClass('visible');
            body.removeClass('modal-open');
        };

        $('#proposal-content-show-history-link').on('click', (e) => {
            e.preventDefault();
            displayModal();
        });

        $('#proposal-content-history-button-close').on('click', () => {
            hideModal();
        });
    });
</script>
