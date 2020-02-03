<?php


namespace app\controllers;


use app\controllers\mainController\MainController;
use app\models\databaseModels\Proposal;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class ProposalController extends MainController
{
    public function actionMyProposals()
    {
    /* 1 liste qui affiche les propositions en cours de reviews
    1 liste qui affiche les propositions reviewées et stipule si elles ont été acceptées,
    acceptées et publiées ou refusées.
    */


    }

    private function getMyPendingProposals()
    {
        $myPendingProposals = new ActiveDataProvider([
            'query' => Proposal::find()
                ->select('proposal.*,
                                    CASE
                                        WHEN 
                                            (SELECT count(*) 
                                            FROM review 
                                            WHERE review.proposal_id = proposal.id) > 0 
                                            THEN 1
                                        ELSE 0
                                      END as has_review')
                ->where(['submitter_id' => self::getCurrentUser()->id])
                ->andWhere(['status' => 'pending']),
            'pagination' => [
                'pageSize' => 20,
                'defaultPageSize' => 20
            ],
            'sort' => [
                'attributes' => ['date' ,'title', 'has_review'],
                'defaultOrder' => ['date', 'title', 'has_review']
            ]
        ]);

        return $myPendingProposals;
    }

    /**
     * Returns proposals that can be access by a rewiewer depending his activity
     *
     * @return string
     */
    public function actionReviewerPendingProposals(): string
    {
        $noReviewedProposalsByAReviewerDataProvider = $this->getNoReviewedAndNoPublishedProposalsForAReviewer();
        $reviewedProposalsByAReviewerDataProvider = $this->getReviewedAndNoPublishedProposalsForAReviewer();

        return $this->render('reviewer-pending-proposals', [
            'noReviewedProposalsByAReviewerDataProvider' => $noReviewedProposalsByAReviewerDataProvider,
            'reviewedProposalsByAReviewerDataProvider' => $reviewedProposalsByAReviewerDataProvider
        ]);
    }

    /**
     * Returns proposals not reviewed and not published for a reviewer
     *
     * @return ActiveDataProvider
     */
    private function getNoReviewedAndNoPublishedProposalsForAReviewer(): ActiveDataProvider
    {
        $noReviewedAndNoPublishedProposalsForAReviewer = new ActiveDataProvider([
            'query' => Proposal::find()
               ->select('proposal.*,
                                  CASE
                                    WHEN 
                                        (SELECT count(*) 
                                        FROM review 
                                        WHERE review.proposal_id = proposal.id) > 0 
                                        THEN 1
                                    ELSE 0
                                  END as has_review')
               ->where([
                    'not in',
                    'id',
                    (new Query())
                        ->select('proposal_id')
                        ->from('review')
                        ->where(['reviewer_id' => self::getCurrentUser()->id])
                ])
                ->andWhere(['published' => false]),
            'pagination' => [
                'pageSize' => 20,
                'defaultPageSize' => 20
            ],
            'sort' => [
                'attributes' => ['has_review','date', 'title'],
                'defaultOrder' => [
                    'has_review' => SORT_ASC,
                    'date' => SORT_DESC
                ]
            ]
        ]);

        return $noReviewedAndNoPublishedProposalsForAReviewer;
    }

    /**
     * Returns proposals already reviewed but not published for a reviewer
     *
     * @return ActiveDataProvider
     */
    private function getReviewedAndNoPublishedProposalsForAReviewer(): ActiveDataProvider
    {
        $reviewedAndNoPublishedProposalsForAReviewer = new ActiveDataProvider([
            'query' => Proposal::find()
            ->where([
                'in',
                'id',
                (new Query())
                    ->select('proposal_id')
                    ->from('review')
                    ->where(['reviewer_id' => self::getCurrentUser()->id])
                    ->column()
            ])
            ->andWhere(['published' => false]),
            'pagination' => [
                'pageSize' => 20,
                'defaultPageSize' => 20
            ],
            'sort' => [
                'attributes' => ['date', 'title'],
                'defaultOrder' => [
                    'date' => SORT_DESC,
                    'title' => SORT_ASC
                ]
            ]
        ]);

        return $reviewedAndNoPublishedProposalsForAReviewer;
    }

}