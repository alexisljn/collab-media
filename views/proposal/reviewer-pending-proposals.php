<?php
/** @var \yii\data\ActiveDataProvider $noReviewedProposalsByAReviewerDataProvider */
/** @var \yii\data\ActiveDataProvider $reviewedProposalsByAReviewerDataProvider */


?>
<strong>No reviewed proposals for a reviewer</strong>
<?=
\yii\grid\GridView::widget([
    'dataProvider' => $noReviewedProposalsByAReviewerDataProvider,
    'columns' => [
        [
            'attribute' => 'title',
            'label' => 'Title',
            'format' => 'raw',
            'value' => function($proposal)
            {

                /** @var \app\models\databaseModels\Proposal $proposal */

                return '<a href="#">' . \yii\helpers\Html::encode($proposal->title) . '</a>';
                // return '<a data-pjax=0 href="/account/user/' . $user->id . '">' . Html::encode($user->first_name . ' ' . $user->last_name) . '</a>';
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

    ]
]) ?>
<strong>reviewed proposals for a reviewer</strong>
<?=
\yii\grid\GridView::widget([
    'dataProvider' => $reviewedProposalsByAReviewerDataProvider,
    'columns' => [
        [
            'attribute' => 'title',
            'label' => 'Title',
            'format' => 'raw',
            'value' => function($proposal)
            {

                /** @var \app\models\databaseModels\Proposal $proposal */

                return '<a href="#">' . \yii\helpers\Html::encode($proposal->title) . '</a>';
                // return '<a data-pjax=0 href="/account/user/' . $user->id . '">' . Html::encode($user->first_name . ' ' . $user->last_name) . '</a>';
            }
        ],
        [
            'attribute' => 'id',
            'label' => 'id'
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

    ]
]) ?>