<?php
/** @var \yii\data\ActiveDataProvider $approvedProposals  */
/**  @var \yii\data\ActiveDataProvider $notApprovedProposals*/
?>

<div class="row">
    <div class="col-3">
        <div class="card bg-dashboard-proposals-count">
            <div class="card-body">
                <p class="h1-dashboard-card"></>
                <div class="card-divider"></div>
                <p class="content-dashboard-card"><i class="fas fa-flag " style="margin-right: 10px;"></i>Proposals's total</p>
            </div>
        </div>
    </div>
    <div class="col-4">
        <div class="card bg-dashboard-proposals-published-count">
            <div class="card-body">
                <h1>45</h1>
               <p>Published proposal's total</p>
            </div>
        </div>
    </div>
    <div class="col-4">
        <div class="card bg-dashboard-proposals-pending-count">
            <div class="card-body">
                This is some text within a card body.
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-4">
        <div class="card bg-dashboard-proposals-rejected-count">
            <div class="card-body">
                This is some text within a card body.
            </div>
        </div>
    </div>
    <div class="col-4">
        <div class="card bg-dashboard-proposals-reviewed-by-user-count">
            <div class="card-body">
                This is some text within a card body.
            </div>
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
