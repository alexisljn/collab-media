<?php
/** @var \yii\data\ActiveDataProvider $approvedProposals  */
/** @var \yii\data\ActiveDataProvider $notApprovedProposals*/
/** @var \yii\data\ActiveDataProvider $publishedProposals */
/** @var \yii\data\ActiveDataProvider $rejectedProposals */
/** @var int $proposalsCount */
/** @var int $publishedProposalsCount  */
/** @var int $pendingProposalsCount */
/** @var int $rejectedProposalsCount */
/** @var int $proposalsReviewedByUserCount */
/** @var int $proposalsCreatedByUserCount */
/** @var int $userProposalsPublishedCount */
?>
<div class="row">
    <h1 class="full-border">Dashboard</h1>
</div>
<div class="row margin-bottom">
    <div class="col-lg-3 col-md-6">
        <div class="card bg-dashboard-proposals-count">
            <div class="card-body">
                <p class="h1-dashboard-card"><?= $proposalsCount ?></p>
                <div class="card-divider"></div>
                <p class="content-dashboard-card"><i class="fas fa-flag" style="margin-right: 10px;"></i>Proposals </p>
            </div>
        </div>
        <div class="spacing_bottom"></div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card bg-dashboard-proposals-published-count">
            <div class="card-body">
                <p class="h1-dashboard-card"><?= $publishedProposalsCount ?></p>
                <div class="card-divider"></div>
                <p class="content-dashboard-card"><i class="fas fa-check" style="margin-right: 10px;"></i>Published</p>
            </div>
        </div>
        <div class="spacing_bottom"></div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card bg-dashboard-proposals-pending-count">
            <div class="card-body">
                <p class="h1-dashboard-card"><?= $pendingProposalsCount ?></p>
                <div class="card-divider"></div>
                <p class="content-dashboard-card"><i class="fas fa-hourglass-half" style="margin-right: 10px;"></i>Pending</p>
            </div>
        </div>
        <div class="spacing_bottom"></div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card bg-dashboard-proposals-rejected-count">
            <div class="card-body">
                <p class="h1-dashboard-card"><?= $rejectedProposalsCount ?></p>
                <div class="card-divider"></div>
                <p class="content-dashboard-card"><i class="fas fa-times" style="margin-right: 10px;"></i>Rejected</p>
            </div>
        </div>
        <div class="spacing_bottom"></div>
    </div>
</div>
 <div class="row margin-bottom">
    <div class="col-lg-4 col-md-12">
        <div class="reviewed-proposals-minicard">
            <span class="text-minicard">Proposals reviewed by you</span> <span class="number-minicard"><?= $proposalsReviewedByUserCount ?></span>
        </div>
        <div class="spacing_bottom"></div>
    </div>
     <div class="col-lg-4 col-md-12">
         <div class="created-proposals-minicard">
             <span class="text-minicard">Proposals created by you</span> <span class="number-minicard"><?= $proposalsCreatedByUserCount ?></span>
         </div>
         <div class="spacing_bottom"></div>
     </div>
     <div class="col-lg-4 col-md-12">
         <div class="published-proposals-minicard">
             <span class="text-minicard">Your proposals published</span> <span class="number-minicard"><?= $userProposalsPublishedCount ?></span>
         </div>
         <div class="spacing_bottom"></div>
     </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <ul class="nav nav-pills mb-3" id="dashboard-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active"
                   id="approved-proposals-tab"
                   data-toggle="tab"
                   href="#approved-proposals"
                   role="tab"
                   aria-controls="approved-proposals"
                   aria-selected="true">Approved proposals</a>
            </li>
            <li class="nav-item">
                <a class="nav-link"
                   id="not-approved-proposals-tab"
                   data-toggle="tab"
                   href="#not-approved-proposals"
                   role="tab"
                   aria-controls="not-approved-proposals"
                   aria-selected="false">Not approved proposals</a>
            </li>
            <li class="nav-item">
                <a class="nav-link"
                   id="published-proposals-tab"
                   data-toggle="tab"
                   href="#published-proposals"
                   role="tab"
                   aria-controls="published-proposals"
                   aria-selected="false">Published proposals</a>
            </li>
            <li class="nav-item">
                <a class="nav-link"
                   id="rejected-proposals-tab"
                   data-toggle="tab"
                   href="#rejected-proposals"
                   role="tab"
                   aria-controls="published-proposals"
                   aria-selected="false">Rejected proposals</a>
            </li>
        </ul>
    </div>
</div>
<div class="row">
    <div id="dashboard-tabs-content" class="col-lg-12 tab-content">
        <div id="approved-proposals"
             class="tab-pane fade show active"
             role="tabpanel"
             aria-labelledby="approved-proposals-tab">
            <?php \yii\widgets\Pjax::begin();
            echo yii\grid\GridView::widget([
                'dataProvider' => $approvedProposals,
                'columns' => [
                    [
                        'attribute' => 'title',
                        'label' => 'Title',
                        'format' => 'raw',
                        'value' => function($proposal)
                        {
                            /** @var \app\models\Proposal $proposal */
                            return '<a data-pjax="0" href="/proposal/proposal/'. $proposal->id . '">' . \yii\helpers\Html::encode($proposal->title) . '</a>';
                        }
                    ],
                    [
                        'attribute' => 'date',
                        'label' => 'Creation date',
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'Rating',
                        'format' => 'raw',
                        'value' => function($proposal)
                        {
                            /** @var \app\models\databaseModels\Proposal $proposal */
                            $approvalsCount = $proposal->getReviews()->where(['status' => \app\models\Review::REVIEW_STATUS_APPROVED])->count();
                            $disapprovalsCount = $proposal->getReviews()->where(['status' => \app\models\Review::REVIEW_STATUS_DISAPPROVED])->count();
                            ob_start();
                            ?>
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
                                } ?>
                                <div class="rating-viewer-approval-bar" style="width: <?= $barPercentage ?>%"></div>
                            </div>
                            <?php  return ob_get_clean();
                        }
                    ],
                    [
                        'label' => 'Comments',
                        'value' => function($proposal)
                        {
                            /** @var \app\models\databaseModels\Proposal $proposal */

                            return $proposal->getComments()->count();
                        }
                    ]
                ]
            ]);
            \yii\widgets\Pjax::end()?>
        </div>
        <div id="not-approved-proposals"
             class="tab-pane fade"
             role="tabpanel"
             aria-labelledby="not-approved-proposals-tab">
            <?php \yii\widgets\Pjax::begin();
            echo yii\grid\GridView::widget([
                'dataProvider' => $notApprovedProposals,
                'columns' => [
                    [
                        'attribute' => 'title',
                        'label' => 'Title',
                        'format' => 'raw',
                        'value' => function($proposal)
                        {
                            /** @var \app\models\databaseModels\Proposal $proposal */
                            return '<a data-pjax="0" href="/proposal/proposal/'. $proposal->id . '">' . \yii\helpers\Html::encode($proposal->title) . '</a>';
                        }
                    ],
                    [
                        'attribute' => 'date',
                        'label' => 'Creation date',
                        'format' => 'raw',
                    ],
                    [
                        'label' => 'Rating',
                        'format' => 'raw',
                        'value' => function($proposal)
                        {
                            /** @var \app\models\databaseModels\Proposal $proposal */
                            $approvalsCount = $proposal->getReviews()->where(['status' => \app\models\Review::REVIEW_STATUS_APPROVED])->count();
                            $disapprovalsCount = $proposal->getReviews()->where(['status' => \app\models\Review::REVIEW_STATUS_DISAPPROVED])->count();
                            ob_start();
                            ?>
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
                                } ?>
                                <div class="rating-viewer-approval-bar" style="width: <?= $barPercentage ?>%"></div>
                            </div>
                            <?php  return ob_get_clean();
                        }
                    ],
                    [
                        'label' => 'Comments',
                        'value' => function($proposal)
                        {
                            /** @var \app\models\databaseModels\Proposal $proposal */

                            return $proposal->getComments()->count();
                        }
                    ]
                ]
            ]);
            \yii\widgets\Pjax::end();?>
        </div>
        <div id="published-proposals"
             class="tab-pane fade"
             role="tabpanel"
             aria-labelledby="published-proposals-tab">
            <?php \yii\widgets\Pjax::begin();
            echo \yii\grid\GridView::widget([
                'dataProvider' => $publishedProposals,
                'columns' => [
                    [
                        'attribute' => 'title',
                        'label' => 'Title',
                        'format' => 'raw',
                        'value' => function($proposal)
                        {
                            /** @var \app\models\databaseModels\Proposal $proposal */
                            return '<a data-pjax="0" href="/proposal/proposal/'. $proposal->id . '">' . \yii\helpers\Html::encode($proposal->title) . '</a>';
                        }
                    ],
                    [
                        'attribute' => 'date',
                        'label' => 'Creation date',
                        'format' => 'raw',
                    ],
                    [
                            'attribute' => 'social_media',
                            'label' => 'Published on',
                            'format' => 'raw'
                    ]
                ]
            ]);
            \yii\widgets\Pjax::end()?>
        </div>
        <div id="rejected-proposals"
             class="tab-pane fade"
             role="tabpanel"
             aria-labelledby="rejected-proposals-tab">
            <?php \yii\widgets\Pjax::begin();
            echo \yii\grid\GridView::widget([
                'dataProvider' => $rejectedProposals,
                'columns' => [
                    [
                        'attribute' => 'title',
                        'label' => 'Title',
                        'format' => 'raw',
                        'value' => function($proposal)
                        {
                            /** @var \app\models\databaseModels\Proposal $proposal */
                            return '<a data-pjax="0" href="/proposal/proposal/'. $proposal->id . '">' . \yii\helpers\Html::encode($proposal->title) . '</a>';
                        }
                    ],
                    [
                        'attribute' => 'date',
                        'label' => 'Creation date',
                        'format' => 'raw',
                    ],
                ]
            ]);
            \yii\widgets\Pjax::end()?>
        </div>
    </div>
</div>