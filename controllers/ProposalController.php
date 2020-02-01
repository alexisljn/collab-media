<?php


namespace app\controllers;


use app\controllers\mainController\MainController;
use app\models\databaseModels\Proposal;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class ProposalController extends MainController
{
    public function actionMyPendingProposals()
    {
       // Yii::app()->user

    }


    public function actionReviewerPendingProposals()
    {
        /*
        1 liste qui affiche les propositions non reviewées et qui n'ont pas été publiées
        1 liste qui affiche les propositions qu'il a reviewées et qui n'ont pas été publiées

        */
        $proposalsDataProvider = $this->getNoReviewedAndNoPublishedProposalsForAReviewer();

        return $this->render('reviewer-pending-proposals', [
            'proposalsDataProvider' => $proposalsDataProvider
        ]);
    }

    private function getNoReviewedAndNoPublishedProposalsForAReviewer(): ?ActiveDataProvider
    {
        $noReviewedAndNoPublishedProposalsForAReviewer = new ActiveDataProvider([
            'query' => Proposal::find()
               ->select('proposal.*,
                                  CASE
                                    WHEN (SELECT count(*) FROM review WHERE review.proposal_id = proposal.id) > 0 THEN 1
                                    ELSE 0
                                  END as has_review')
               ->where([
                    'not in',
                    'id',
                    (new Query())
                        ->select('proposal_id')
                        ->from('review')
                        ->where(['reviewer_id' => 13/*currentUser->id*/])
                ])
                ->andWhere(['published' => false]),
            'pagination' => [
                'pageSize' => 20,
                'defaultPageSize' => 20
            ],
            'sort' => [
                'attributes' => ['has_review','date', 'title', 'id'],
                'defaultOrder' => [
                    'has_review' => SORT_ASC,
                    'date' => SORT_DESC,
                ]
            ]
        ]);
        return $noReviewedAndNoPublishedProposalsForAReviewer;
    }

    private function getReviewedAndNoPublishedProposalsForAReviewer()
    {
        $reviewedAndNoPublishedProposalsForAReviewer = new ActiveDataProvider([
            'query' => Proposal::find()
            /*>where([
                'in',
                'id',
                (new Query())
                    ->select('proposal_id')
                    ->from('review')
                    ->where(['reviewer_id' => //CURRENT_USER_ID])
                    ->column()
            ])*/
            ->andWhere(['published' => false])
        ]);
    }

}