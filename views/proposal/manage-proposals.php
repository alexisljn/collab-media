<?php
/** @var \yii\data\ActiveDataProvider $approvedProposals  */
/**  @var \yii\data\ActiveDataProvider $notApprovedProposals*/
?>
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
                /** @var \app\models\databaseModels\Proposal $proposal */
                /** @TODO link manage-proposal/id */
                return '<a href="#">' . \yii\helpers\Html::encode($proposal->title) . '</a>';
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
                return '<a href="#">' . \yii\helpers\Html::encode($proposal->title) . '</a>';
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
        ]
    ]
]);
?>
