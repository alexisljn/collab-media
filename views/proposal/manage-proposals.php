<?php
/** @var \yii\data\ActiveDataProvider $approvedProposals  */
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