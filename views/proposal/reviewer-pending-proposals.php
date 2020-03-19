<?php
/** @var \yii\data\ActiveDataProvider $proposalsToReviewActiveDataProvider */
/** @var \yii\data\ActiveDataProvider $reviewedProposalsActiveDataProvider */
?>
<div class="row">
    <div class="col-12">
        <h1>Pending proposals</h1>
    </div>
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
                'emptyText' => 'No proposals to review',
                'layout' => '{items}{pager}',
                'tableOptions' => ['class' => 'table box'],
                'headerRowOptions' => ['class' => 'box-header'],
                'rowOptions' => ['class' => 'box-row js-row-clickable'],
                'columns' => [
                    [
                        'attribute' => 'title',
                        'contentOptions' => ['class' => 'box-link'],
                        'label' => 'Title',
                        'format' => 'raw',
                        'value' => function($proposal)
                        {

                            /** @var \app\models\databaseModels\Proposal $proposal */

                            return '<a data-pjax="0" data-js-row-clickable-url href="/proposal/proposal/'. $proposal->id . '">' . \yii\helpers\Html::encode($proposal->title) . '</a>';
                        }
                    ],
                    [
                        'attribute' => 'date',
                        'label' => 'Date'
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
                        'contentOptions' => ['class' => 'text-center'],
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
                ],
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
                'emptyText' => 'No proposals reviewed',
                'layout' => '{items}{pager}',
                'tableOptions' => ['class' => 'table box'],
                'headerRowOptions' => ['class' => 'box-header'],
                'rowOptions' => ['class' => 'box-row js-row-clickable'],
                'columns' => [
                    [
                        'attribute' => 'title',
                        'label' => 'Title',
                        'contentOptions' => ['class' => 'box-link'],
                        'format' => 'raw',
                        'value' => function($proposal)
                        {

                            /** @var \app\models\databaseModels\Proposal $proposal */

                            return '<a data-pjax="0" data-js-row-clickable-url href="/proposal/proposal/'. $proposal->id . '">' . \yii\helpers\Html::encode($proposal->title) . '</a>';
                        }
                    ],
                    [
                        'attribute' => 'date',
                        'label' => 'Date'
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
                        'contentOptions' => ['class' => 'text-center'],
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
                ],
            ]);
            \yii\widgets\Pjax::end();?>
    </div>
</div>
