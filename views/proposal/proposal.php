<?php
/** @var \app\models\databaseModels\Proposal $selectedProposal */
/** @var \app\models\databaseModels\ProposalContentHistory $lastProposalContent */
/** @var \app\models\databaseModels\Review|\app\models\databaseModels\Comment|\app\models\databaseModels\ProposalContentHistory $chronologicalStream */
/** @var int $approvalsCount */
/** @var int $disapprovalsCount */
/** @var \app\models\forms\ManageProposalForm $manageProposalFormModel */
/** @var \app\models\forms\ManageCommentForm $manageCommentFormModel */
/** @var bool|\app\models\Review $potentialReview */
/** @var bool $canEditProposal */
/** @var bool $canPublishProposal */

use app\controllers\mainController\MainController;
use app\models\Proposal;
use yii\helpers\Html;
use yii\widgets\ActiveForm; ?>
<div id="work"></div>
<div id="proposal-content-history-modal" class="modal-container">
    <div class="modal-content">
        <button id="proposal-content-history-button-close" class="modal-close-button"><i class="fas fa-times" style="font-size: 1.3em"></i></button>
        <h1>Editions history</h1>

        <?php foreach ($chronologicalStream as $oldProposalContent) {
            if ($oldProposalContent instanceof \app\models\databaseModels\ProposalContentHistory) {
                ?>
                <div class="proposal-content-history-element-container">
                    <p class="proposal-content-history-element-date">
                        <?= $oldProposalContent->date ?>
                        <?php
                        if ($oldProposalContent->date === $lastProposalContent->date) {
                            ?>
                            <strong>(current)</strong>
                            <?php
                        }
                        ?>
                    </p>
                    <div class="proposal-content-text">
                        <?= (new Parsedown())
                            ->text(Html::encode($oldProposalContent->content)) ?>
                    </div>
                </div>
            <?php }
        } ?>
    </div>
</div>

<!-- Proposal informations -->
<div class="row">
    <div class="col-12">
        <h1><?= Html::encode($selectedProposal->title) ?></h1>
    </div>
</div>

<div class="row proposal-content">
    <div class="col-9 proposal-timeline">
        <!-- Proposal informations -->
        <?php if (!is_null($selectedProposal->social_media)) { ?>
            <p class="content-layout">Published on : <?= $selectedProposal->social_media ?></p>
        <?php } ?>
        <div class="proposal-timeline-text-element-container content-layout" id="proposal-content">
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
            <div class="proposal-timeline-text-element-content">
                <?= (new Parsedown())->text(Html::encode($lastProposalContent->content)) ?>
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
        <div style="display: none;" class="form-layout proposal-edit-form-container">
            <?php
            $form = yii\widgets\ActiveForm::begin([
                'id' => 'proposal-form',
                'action' => '/proposal/edit-proposal/'. $selectedProposal->id,
            ]);
            ?>
            <?= $form->field($manageProposalFormModel, 'title')->textInput(['id' => 'proposal-form-title-input']); ?>
            <?= $form->field($manageProposalFormModel, 'content')->hiddenInput(['id' => 'proposal-form-content-input'])->label(false); ?>
            <div class="proposal-edit-section"></div>
            <?= $form->field($manageProposalFormModel, 'relatedFile')->fileInput(); ?>
            <?= yii\helpers\Html::submitButton('Edit', ['class' => 'btn btn-not-outline']); ?>
            <?php yii\widgets\ActiveForm::end(); ?>
        </div>

        <!-- Chronological Stream -->
        <div id="chronological-stream">
            <?php
            foreach ($chronologicalStream as $chronologicalItem) {
                if ($chronologicalItem instanceof \app\models\databaseModels\Comment) {
                    ?>
                    <div class="proposal-timeline-text-element-container">
                        <div class="proposal-timeline-text-element-header">
                            <div style="float: left">
                                <?= Html::encode($chronologicalItem->author->firstname . ' ' . $chronologicalItem->author->lastname) ?>
                                –
                                <?= $chronologicalItem->date ?>
                                <?php
                                if(!is_null($chronologicalItem->edited_date)) {
                                    ?>
                                    – Edited at <?= $chronologicalItem->edited_date ?>
                                    <?php
                                }
                                ?>
                            </div>
                            <?php
                            if($chronologicalItem->author_id === MainController::getCurrentUser()->id) {
                                ?>
                                <div style="float: right">

                                    <a href="" id="edit-comment-link-<?= $chronologicalItem->id ?>">Edit</a>
                                    <a href="" id="cancel-edit-link-<?= $chronologicalItem->id ?>" style="display: none;">Cancel</a>
                                </div>
                                <?php
                            }
                            ?>
                            <div class="clear"></div>
                        </div>
                        <div class="proposal-timeline-text-element-content">
                            <?php
                            if ($chronologicalItem->author_id == MainController::getCurrentUser()->id) {
                                ?>

                                <div id="edit-comment-<?= $chronologicalItem->id ?>" style="display: none">
                                    <?php
                                    $manageCommentForm = yii\widgets\ActiveForm::begin([
                                        'id' => 'comment-form-' . $chronologicalItem->id,
                                        'action' => '/proposal/edit-comment/'. $selectedProposal->id,
                                    ]);
                                    ?>
                                    <?= $manageCommentForm
                                        ->field($manageCommentFormModel, 'content')
                                        ->textarea([
                                            'id' => 'edit-comment-content-' . $chronologicalItem->id,
                                            'rows' => '8',
                                        ])
                                        ->label(false);
                                    ?>
                                    <?= $manageCommentForm
                                        ->field($manageCommentFormModel, 'id')
                                        ->hiddenInput(['id' => 'edit-comment-id-input-' . $chronologicalItem->id])
                                        ->label(false);
                                    ?>
                                    <?= yii\helpers\Html::submitButton('Edit', ['class' => 'btn btn-not-outline']); ?>
                                    <?php yii\widgets\ActiveForm::end(); ?>
                                </div>
                                <?php
                            }
                            ?>
                            <div id="comment-layout-<?= $chronologicalItem->id ?>">
                                <p id="comment-content-<?= $chronologicalItem->id ?>">
                                    <?= nl2br(Html::encode($chronologicalItem->content)) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php
                } elseif ($chronologicalItem instanceof \app\models\databaseModels\Review
                    && $chronologicalItem->status != \app\models\Review::REVIEW_STATUS_CANCELLED) {
                    ?>
                    <div class="proposal-timeline-notification-element-container">
                        <div class="proposal-timeline-notification-element-icon">
                            <?php
                            switch($chronologicalItem->status) {
                                case \app\models\Review::REVIEW_STATUS_APPROVED:
                                    ?>
                                    <i class="fas fa-thumbs-up"></i>
                                    <?php
                                    break;
                                case \app\models\Review::REVIEW_STATUS_DISAPPROVED:
                                    ?>
                                    <i class="fas fa-thumbs-down"></i>
                                    <?php
                                    break;
                            }
                            ?>
                        </div>
                        <div class="proposal-timeline-notification-element-content">
                            <?= $chronologicalItem->date ?>
                            –
                            <?= Html::encode($chronologicalItem->reviewer->firstname . ' ' . $chronologicalItem->reviewer->lastname) ?>
                            <?php
                            switch($chronologicalItem->status) {
                                case \app\models\Review::REVIEW_STATUS_APPROVED:
                                    ?>
                                    approved
                                    <?php
                                    break;
                                case \app\models\Review::REVIEW_STATUS_DISAPPROVED:
                                    ?>
                                    disapproved
                                    <?php
                                    break;
                            }
                            ?>
                            this proposal
                        </div>
                    </div>
                    <?php
                } elseif ($chronologicalItem instanceof \app\models\databaseModels\ProposalContentHistory) {
                    if ($chronologicalItem->date != $selectedProposal->date) {
                        ?>
                        <div class="proposal-timeline-notification-element-container">
                            <div class="proposal-timeline-notification-element-icon">
                                <i class="fas fa-pen"></i>
                            </div>
                            <div class="proposal-timeline-notification-element-content">
                                <?= $chronologicalItem->date ?>
                                –
                                <?= Html::encode($selectedProposal->submitter->firstname . ' ' . $selectedProposal->submitter->lastname) ?>
                                edited the proposal content
                            </div>
                        </div>
                        <?php
                    }
                } elseif ($chronologicalItem instanceof \app\models\databaseModels\ProposalFileHistory) {
                    if ($chronologicalItem->date != $selectedProposal->date) {
                        ?>
                        <div class="proposal-timeline-notification-element-container">
                            <div class="proposal-timeline-notification-element-icon">
                                <i class="fas fa-file" style="margin-left: 3px"></i>
                            </div>
                            <div class="proposal-timeline-notification-element-content">
                                <?= $chronologicalItem->date ?>
                                –
                                <?= Html::encode($selectedProposal->submitter->firstname . ' ' . $selectedProposal->submitter->lastname) ?>
                                updated the proposal file
                            </div>
                        </div>
                        <?php
                    }
                }
            }
            ?>
        </div>
        <div class="proposal-add-comment-container">
            <h3>Add a comment</h3>
            <?php
            $manageCommentForm = yii\widgets\ActiveForm::begin([
                'id' => 'comment-form',
                'action' => '/proposal/post-comment/'. $selectedProposal->id,
            ]);
            ?>
            <?= $manageCommentForm
                ->field($manageCommentFormModel, 'content')
                ->textarea([
                    'id' => 'proposal-comment-content-input',
                    'rows' => '8',
                ])
                ->label(false); ?>
            <?= yii\helpers\Html::submitButton('Submit', ['class' => 'btn btn-not-outline']); ?>
            <?php yii\widgets\ActiveForm::end(); ?>
        </div>
    </div>

    <aside class="col-3 proposal-sidebar">
        <div class="proposal-sidebar-block">
            <!--        TODO Display this block only if the current user is not the proposal submitter -->
            Created by <strong><?= Html::encode($selectedProposal->submitter->firstname . ' ' . $selectedProposal->submitter->lastname) ?></strong>
        </div>
        <div class="proposal-sidebar-divider"></div>
        <?php if ($canEditProposal) { ?>
            <div class="proposal-sidebar-block">
                <button id="edit-link" class="btn btn-block btn-sm btn-not-outline">Edit</button>
            </div>
            <div class="proposal-sidebar-divider"></div>
        <?php } ?>
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
        <?php if (!is_null($potentialReview)) { ?>
            <div class="proposal-sidebar-divider"></div>
            <div class="proposal-sidebar-block">
                <div class="row">
                    <div class="col-6 text-center">
                        <button id="vote-up" type="button" class="btn btn-lg btn-outline-success">
                            <i class="fas fa-thumbs-up"></i>
                        </button>
                    </div>
                    <div class="col-6 text-center">
                        <button id="vote-down" type="button" class="btn btn-lg btn-outline-danger">
                            <i class="fas fa-thumbs-down"></i>
                        </button>
                    </div>
                </div>
            </div>
        <?php } ?>
        <?php if ($canPublishProposal) { ?>
            <div class="proposal-sidebar-divider"></div>
            <div class="proposal-sidebar-block">
                <div class="row">
                    <div class="col-6 text-center">
                        <button id="publish-btn" type="button" class="btn btn-lg btn-success">
                            Publish
                        </button>
                    </div>
                    <div class="col-6 text-center">
                        <button id="reject-btn" type="button" class="btn btn-lg btn-danger">
                            Reject
                        </button>
                    </div>
                </div>
            </div>
        <?php } ?>

    </aside>
</div>

<?php if ($canEditProposal) { ?>
<script type="text/javascript" id="edit-form-script">
    $(() => {
        const editor = new tui.Editor({
            el: document.querySelector('.proposal-edit-section'),
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
        $('#proposal-form').on("submit", () => {
            $("#proposal-form-content-input").val(editor.getMarkdown());
        });
    });
</script>
<?php } ?>
<script type="text/javascript" id="comment-script">
    $(() => {
        $("a[id^='edit-comment-link-']").on('click', (event) => {
            const commentId = event.target.id.split('-')[event.target.id.split('-').length -1];
            $('#edit-comment-'+commentId+', #cancel-edit-link-'+commentId).css('display', 'block');
            $('#edit-comment-link-'+commentId+', #comment-layout-'+commentId).css('display', 'none');
            $('#edit-comment-content-'+commentId).val($('#comment-content-'+commentId).text().trim());
            event.preventDefault();
        });
        $("a[id^='cancel-edit-link-']").on('click', (event) => {
            const commentId = event.target.id.split('-')[event.target.id.split('-').length -1];
            $('#edit-comment-'+commentId+', #cancel-edit-link-'+commentId).css('display', 'none');
            $('#edit-comment-link-'+commentId+', #comment-layout-'+commentId).css('display', 'block');
            event.preventDefault();
        });
        $("form[id^='comment-form-']").on('submit', (event) => {
            const commentId = event.target.id.split('-')[event.target.id.split('-').length -1];
            $("#edit-comment-id-input-"+commentId).val(commentId);
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
<?php if(!is_null($potentialReview)) { ?>
<script type="text/javascript" id="vote-review-script">
    $(() => {
        let reviewStatus = '<?= $potentialReview->status ?>';
        let reviewId = undefined;
        let thumbsUp = $('#vote-up');
        let thumbsDown = $('#vote-down');

        thumbsUp.data('statusOnChange', '<?= \app\models\Review::REVIEW_STATUS_APPROVED?>');
        thumbsDown.data('statusOnChange', '<?= \app\models\Review::REVIEW_STATUS_DISAPPROVED ?>');

        if (reviewStatus.length > 0) {
            reviewId = '<?= $potentialReview->id ?>';

            switch (reviewStatus) {
                case '<?= \app\models\Review::REVIEW_STATUS_APPROVED ?>':
                    thumbsUp
                        .removeClass('btn-outline-success')
                        .addClass('btn-success')
                        .data('statusOnChange', '<?= \app\models\Review::REVIEW_STATUS_CANCELLED ?>');
                    break;
                case '<?= \app\models\Review::REVIEW_STATUS_DISAPPROVED ?>':
                    thumbsDown
                        .removeClass('btn-outline-danger')
                        .addClass('btn-danger')
                        .data('statusOnChange', '<?= \app\models\Review::REVIEW_STATUS_CANCELLED ?>');
                    break;
            }
        }

        thumbsUp.on('click', () => {
            thumbsUp.toggleClass('btn-outline-success').toggleClass('btn-success');

            if (reviewStatus === '<?= \app\models\Review::REVIEW_STATUS_DISAPPROVED ?>') {
                thumbsDown.removeClass('btn-danger').addClass('btn-outline-danger');
            }

            reviewStatus = thumbsUp.data('statusOnChange');

            $.post('/proposal/post-review',
                {
                    proposalId: <?= $selectedProposal->id ?>,
                    reviewId: reviewId,
                    reviewStatus: thumbsUp.data('statusOnChange')
                },
                (response) => {
                    response = JSON.parse(response);
                    reviewId = response.reviewId;
                    let ratingBar = $(response.html).find('div.rating-viewer-container').html();
                    let chronologicalStream = $(response.html).find('div#chronological-stream').html();
                    $('div.rating-viewer-container').html(ratingBar);
                    $('div#chronological-stream').html(chronologicalStream);

                    if (thumbsUp.data('statusOnChange') === '<?= \app\models\Review::REVIEW_STATUS_APPROVED ?>') {
                        thumbsUp.data('statusOnChange', '<?= \app\models\Review::REVIEW_STATUS_CANCELLED ?>');
                    } else {
                        thumbsUp.data('statusOnChange', '<?= \app\models\Review::REVIEW_STATUS_APPROVED ?>');
                    }

                    thumbsDown.data('statusOnChange', '<?= \app\models\Review::REVIEW_STATUS_DISAPPROVED ?>');
                }
            )
        });

        thumbsDown.on('click', () => {
            thumbsDown.toggleClass('btn-outline-danger').toggleClass('btn-danger');

            if (reviewStatus === '<?= \app\models\Review::REVIEW_STATUS_APPROVED ?>') {
                $('#vote-up').removeClass('btn-success').addClass('btn-outline-success');
            }

            reviewStatus = thumbsDown.data('statusOnChange');

            $.post('/proposal/post-review',
                {
                    proposalId: <?= $selectedProposal->id ?>,
                    reviewId: reviewId,
                    reviewStatus: thumbsDown.data('statusOnChange')
                },
                (response) => {
                    response = JSON.parse(response);
                    reviewId = response.reviewId;
                    let ratingBar = $(response.html).find('div.rating-viewer-container').html();
                    let chronologicalStream = $(response.html).find('div#chronological-stream').html();
                    $('div.rating-viewer-container').html(ratingBar);
                    $('div#chronological-stream').html(chronologicalStream);


                    if (thumbsDown.data('statusOnChange') === '<?= \app\models\Review::REVIEW_STATUS_DISAPPROVED ?>') {
                        thumbsDown.data('statusOnChange', '<?= \app\models\Review::REVIEW_STATUS_CANCELLED ?>');
                    } else {
                        thumbsDown.data('statusOnChange', '<?= \app\models\Review::REVIEW_STATUS_DISAPPROVED ?>');
                    }

                    thumbsUp.data('statusOnChange', '<?= \app\models\Review::REVIEW_STATUS_APPROVED ?>');
                }
            )
        });
    })
</script>
<?php } ?>
<?php if ($canPublishProposal) { ?>
<script type="text/javascript" id="publish-reject">
    $(() => {
        $('#reject-btn').on('click', () => {

            if (confirm('Are you sure to reject this proposal ?')) {
                $.post('/proposal/reject-proposal',
                    { proposalId: <?= $selectedProposal->id ?> },
                    (response) => {
                        $('aside').html($(response).find('aside').html());
                    }
                )
            }
        })
    })
</script>
<?php } ?>