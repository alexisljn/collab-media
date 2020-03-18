<?php
/** @var \yii\data\ActiveDataProvider $myPendingProposals */
/** @var \yii\data\ActiveDataProvider $myNotPendingProposals */
?>

<div class="row">
    <h1 class="full-border">My Proposals</h1>
</div>
<div class="row">
    <div class="col-lg-12">
        <ul class="nav nav-pills mb-3" id="dashboard-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active"
                   id="pending-proposals-tab"
                   data-toggle="tab"
                   href="#pending-proposals"
                   role="tab"
                   aria-controls="pending-proposals"
                   aria-selected="true">Pending proposals</a>
            </li>
            <li class="nav-item">
                <a class="nav-link"
                   id="proposals-history-tab"
                   data-toggle="tab"
                   href="#proposals-history"
                   role="tab"
                   aria-controls="proposals-history"
                   aria-selected="false">Proposals history</a>
            </li>
        </ul>
    </div>
</div>
<div class="row">
    <div id="proposals-tabs-content" class="col-lg-12 tab-content">
        <div id="pending-proposals"
             class="tab-pane fade show active"
             role="tabpanel"
             aria-labelledby="pending-proposals-tab">
            <?php \yii\widgets\Pjax::begin();
            echo \yii\grid\GridView::widget([
                'dataProvider' => $myPendingProposals,
                'emptyText' => 'You have no pending proposals ',
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
        <div id="proposals-history"
             class="tab-pane fade"
             role="tabpanel"
             aria-labelledby="proposals-history-tab">
            <?php \yii\widgets\Pjax::begin();
            echo \yii\grid\GridView::widget([
                'dataProvider' => $myNotPendingProposals,
                'emptyText' => 'No proposals',
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
    </div>
</div>