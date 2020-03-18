<?php
/** @var \yii\data\ActiveDataProvider $proposalsToReviewActiveDataProvider */
/** @var \yii\data\ActiveDataProvider $reviewedProposalsActiveDataProvider */
?>
<div class="row">
    <h1 class="full-border">Pending proposals</h1>
</div>
<div class="row">
    <div class="col-lg-12">
        <ul class="nav nav-pills mb-3" id="dashboard-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active"
                   id="proposals-to-review-tab"
                   data-toggle="tab"
                   href="#proposals-to-review"
                   role="tab"
                   aria-controls="proposals-to-review"
                   aria-selected="true">Proposals to review</a>
            </li>
            <li class="nav-item">
                <a class="nav-link"
                   id="reviewed-proposals-tab"
                   data-toggle="tab"
                   href="#reviewed-proposals"
                   role="tab"
                   aria-controls="reviewed-proposals"
                   aria-selected="false">Reviewed proposals</a>
            </li>
        </ul>
    </div>
</div>
<div class="row">
    <div id="proposals-tabs-content" class="col-lg-12 tab-content">
        <div id="proposals-to-review"
             class="tab-pane fade show active"
             role="tabpanel"
             aria-labelledby="proposals-to-review-tab">
            <?php \yii\widgets\Pjax::begin();
            echo \yii\grid\GridView::widget([
                'dataProvider' => $proposalsToReviewActiveDataProvider,
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

                ]
            ]) ;
            \yii\widgets\Pjax::end();?>
        </div>
        <div id="reviewed-proposals"
             class="tab-pane fade"
             role="tabpanel"
             aria-labelledby="reviewed-proposals-tab">
            <?php
            \yii\widgets\Pjax::begin();
            echo \yii\grid\GridView::widget([
                'dataProvider' => $reviewedProposalsActiveDataProvider,
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

                ]
            ]);
            \yii\widgets\Pjax::end();?>
    </div>
</div>
