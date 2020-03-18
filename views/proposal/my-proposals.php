<?php
/** @var \yii\data\ActiveDataProvider $myPendingProposals */
/** @var \yii\data\ActiveDataProvider $myNotPendingProposals */
?>


<strong>My pending proposals</strong>
<?php \yii\widgets\Pjax::begin(['timeout' => 5000]);
echo \yii\grid\GridView::widget([
    'dataProvider' => $myPendingProposals,
    'columns' => [
        [
            'attribute' => 'title',
            'label' => 'Title',
            'format' => 'raw',
            'value' => function($proposal)
            {
                /** @var \app\models\databaseModels\Proposal $proposal */

                return '<a data-pjax="0" href="proposal/' . $proposal->id . '">' . \yii\helpers\Html::encode($proposal->title) . '</a>';
            }
        ],
        [
            'attribute' => 'date',
            'label' => 'date'
        ],
        [
            'attribute' => 'has_review',
            'label' => 'has review ?',
            'value' => function($proposal) {
                /** @var \app\models\databaseModels\Proposal $proposal */
                if ($proposal->getReviews()->count()) {
                    return 'Yes';
                }
                return 'No';
            }
        ],
        [
            'label' => 'Approval',
            'value' => function($proposal)
            {
                /** @var \app\models\databaseModels\Proposal $proposal */

                return $proposal->getReviews()->where(['status' => 'approved'])->count();
            }
        ],
        [
            'label' => 'Rejected',
            'value' => function($proposal)
            {
                /** @var \app\models\databaseModels\Proposal $proposal */

                return $proposal->getReviews()->where(['status' => 'disapproved'])->count();
            }
        ]
    ],
    'pager' => [
        'maxButtonCount' => 7,
        'firstPageLabel' => '<i class="fas fa-angle-double-left"></i>',
        'lastPageLabel' => '<i class="fas fa-angle-double-right"></i>',
        'prevPageLabel' => '<i class="fas fa-angle-left"></i>',
        'nextPageLabel' => '<i class="fas fa-angle-right"></i>'
    ]
]);
\yii\widgets\Pjax::end();?>
<strong>Proposals history</strong>
<?php \yii\widgets\Pjax::begin(['timeout' => 5000]);
echo \yii\grid\GridView::widget([
    'dataProvider' => $myNotPendingProposals,
    'columns' => [
        [
            'attribute' => 'title',
            'label' => 'Title',
            'format' => 'raw',
            'value' => function($proposal)
            {
                /** @var \app\models\databaseModels\Proposal $proposal */

                return '<a data-pjax="0" href="proposal/' . $proposal->id . '">' . \yii\helpers\Html::encode($proposal->title) . '</a>';
            }
        ],
        [
            'attribute' => 'date',
            'label' => 'date'
        ],
        [
            'label' => 'Approval',
            'value' => function($proposal)
            {
                /** @var \app\models\databaseModels\Proposal $proposal */

                return $proposal->getReviews()->where(['status' => 'approved'])->count();
            }
        ],
        [
            'label' => 'Rejected',
            'value' => function($proposal)
            {
                /** @var \app\models\databaseModels\Proposal $proposal */

                return $proposal->getReviews()->where(['status' => 'disapproved'])->count();
            }
        ]
    ],
    'pager' => [
        'maxButtonCount' => 7,
        'firstPageLabel' => '<i class="fas fa-angle-double-left"></i>',
        'lastPageLabel' => '<i class="fas fa-angle-double-right"></i>',
        'prevPageLabel' => '<i class="fas fa-angle-left"></i>',
        'nextPageLabel' => '<i class="fas fa-angle-right"></i>'
    ]
]);
\yii\widgets\Pjax::end();?>
