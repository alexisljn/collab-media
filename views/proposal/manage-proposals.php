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

<div class="row margin-bottom">
    <div class="col-lg-3 col-md-6">
        <div class="card bg-dashboard-proposals-count">
            <div class="card-body">
                <p class="h1-dashboard-card"><?= $proposalsCount ?></p>
                <div class="card-divider"></div>
                <p class="content-dashboard-card"><i class="fas fa-flag" style="margin-right: 10px;"></i>Proposals</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card bg-dashboard-proposals-published-count">
            <div class="card-body">
                <p class="h1-dashboard-card"><?= $publishedProposalsCount ?></p>
                <div class="card-divider"></div>
                <p class="content-dashboard-card"><i class="fas fa-check" style="margin-right: 10px;"></i>Published proposals</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card bg-dashboard-proposals-pending-count">
            <div class="card-body">
                <p class="h1-dashboard-card"><?= $pendingProposalsCount ?></p>
                <div class="card-divider"></div>
                <p class="content-dashboard-card"><i class="fas fa-hourglass-half" style="margin-right: 10px;"></i>Pending proposals</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card bg-dashboard-proposals-rejected-count">
            <div class="card-body">
                <p class="h1-dashboard-card"><?= $rejectedProposalsCount ?></p>
                <div class="card-divider"></div>
                <p class="content-dashboard-card"><i class="fas fa-times" style="margin-right: 10px;"></i>Rejected proposals</p>
            </div>
        </div>
    </div>
</div>
 <div class="row margin-bottom">
    <div class="col-lg-4 col-md-12">
        <div class="reviewed-proposals-minicard">
            <span class="text-minicard">Proposals reviewed by you</span> <span class="number-minicard"><?= $proposalsReviewedByUserCount ?></span>
        </div>
    </div>
     <div class="col-lg-4 col-md-12">
         <div class="created-proposals-minicard">
             <span class="text-minicard">Proposals created by you</span> <span class="number-minicard"><?= $proposalsCreatedByUserCount ?></span>
         </div>
     </div>
     <div class="col-lg-4 col-md-12">
         <div class="published-proposals-minicard">
             <span class="text-minicard">Your proposals published</span> <span class="number-minicard"><?= $userProposalsPublishedCount ?></span>
         </div>
     </div>
</div>

<strong>Reviewer's approved proposals</strong>
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
<strong>Not approved proposals</strong>
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
