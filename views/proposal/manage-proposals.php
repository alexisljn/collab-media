<?php
/** @var \yii\data\ActiveDataProvider $approvedProposals  */
/** @var \yii\data\ActiveDataProvider $notApprovedProposals*/
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
<div class="row margin-bottom">
    <div class="col-lg-6 offset-lg-3">
        <button id="approved-proposals-btn" class="btn btn-lg btn-block btn-success">Approved proposals</button>
    </div>

    <div class="col-lg-12">
        <div id="approved-proposals" class="d-block">
            <?= yii\grid\GridView::widget([
                'dataProvider' => $approvedProposals,
                'columns' => [
                    [
                        'attribute' => 'title',
                        'label' => 'Title',
                        'format' => 'raw',
                        'value' => function($proposal)
                        {
                            /** @var \app\models\Proposal $proposal */
                            /** @TODO link manage-proposal/id */
                            return '<a href="/proposal/proposal/'. $proposal->id . '">' . \yii\helpers\Html::encode($proposal->title) . '</a>';
                        }
                    ],
                    [
                        'attribute' => 'id',
                        'label' => 'ID',
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'status',
                        'label' => 'Status',
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'count_reviews',
                        'label' => 'Reviews',
                        'format' => 'raw',
                    ]
                ]
            ]);
            ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-6 offset-lg-3">
        <button id="not-approved-proposals-btn" class="btn btn-lg btn-block btn-primary">Not approved proposals</button>
    </div>
    <div class="col-lg-12">
        <div id="not-approved-proposals" class="d-block">
            <?= yii\grid\GridView::widget([
                'dataProvider' => $notApprovedProposals,
                'columns' => [
                    [
                        'attribute' => 'title',
                        'label' => 'Title',
                        'format' => 'raw',
                        'value' => function($proposal)
                        {
                            /** @var \app\models\databaseModels\Proposal $proposal */
                            /** @TODO link manage-proposal/id */
                            return '<a href="/proposal/proposal/'. $proposal->id . '">' . \yii\helpers\Html::encode($proposal->title) . '</a>';
                        }
                    ],
                    [
                        'attribute' => 'id',
                        'label' => 'ID',
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'status',
                        'label' => 'Status',
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'count_reviews',
                        'label' => 'Reviews',
                        'format' => 'raw'
                    ]
                ]
            ]);
            ?>
        </div>
    </div>
</div>
<script type="text/javascript" id="dashboard-proposals">
    $(() => {
        $('#approved-proposals-btn').on('click', () => {
            $('#approved-proposals').toggleClass('d-block').toggleClass('d-none');
        });
        $('#not-approved-proposals-btn').on('click', () => {
            $('#not-approved-proposals').toggleClass('d-block').toggleClass('d-none');
        })
    })
</script>