<?php
/** @var \yii\data\ActiveDataProvider $noReviewedProposalsByAReviewerDataProvider */
/** @var \yii\data\ActiveDataProvider $reviewedProposalsByAReviewerDataProvider */


?>
<strong>No reviewed proposals for a reviewer</strong>
<?php \yii\widgets\Pjax::begin(['timeout' => 5000]);
echo \yii\grid\GridView::widget([
    'dataProvider' => $noReviewedProposalsByAReviewerDataProvider,
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
            'label' => 'Date'
        ],
        [
            'attribute' => 'has_review',
            'label' => 'Reviewed ?',
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
            'label' => 'Disapproval',
            'value' => function($proposal)
            {
                /** @var \app\models\databaseModels\Proposal $proposal */

                return $proposal->getReviews()->where(['status' => 'disapproved'])->count();
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
    ],
    'pager' => [
        'maxButtonCount' => 7,
        'firstPageLabel' => '<i class="fas fa-angle-double-left"></i>',
        'lastPageLabel' => '<i class="fas fa-angle-double-right"></i>',
        'prevPageLabel' => '<i class="fas fa-angle-left"></i>',
        'nextPageLabel' => '<i class="fas fa-angle-right"></i>'
    ]
]) ;
\yii\widgets\Pjax::end();?>
<strong>reviewed proposals for a reviewer</strong>
<?php
\yii\widgets\Pjax::begin(['timeout' => 5000]);
echo \yii\grid\GridView::widget([
    'dataProvider' => $reviewedProposalsByAReviewerDataProvider,
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
            'label' => 'Date'
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
            'label' => 'Disapproval',
            'value' => function($proposal)
            {
                /** @var \app\models\databaseModels\Proposal $proposal */

                return $proposal->getReviews()->where(['status' => 'disapproved'])->count();
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
