<?php


namespace app\controllers;


use app\controllers\mainController\MainController;
use app\models\databaseModels\Comment;
use app\models\databaseModels\Proposal;
use app\models\databaseModels\ProposalContentHistory;
use app\models\databaseModels\Review;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use yii\db\Query;
use yii\web\NotFoundHttpException;

class ProposalController extends MainController
{
    /**
     * Returns all proposals submitted by a member or a single proposal if
     * an Id was specified in url.
     *
     * @param null|int $id
     * @return string
     * @throws Exception
     */
    public function actionMyProposals(int $id = null): string
    {
        if (!is_null($id)) {
            $selectedProposal = $this->checkIfProposalExists($id);
            $this->checkIfUserIsOwnerOfProposal($selectedProposal->submitter->id);
            $chronologicalStream = $this->generateChronologicalStream
            (
                $selectedProposal->comments,
                $selectedProposal->reviews,
                $selectedProposal->proposalContentHistories
            );
            $chronologicalStream = $this->sortChronologicalStreamByDate($chronologicalStream);
            $lastProposalContent = $selectedProposal->proposalContentHistories[
                count($selectedProposal->proposalContentHistories)-1
            ];
            return $this->render('my-proposal', [
                'selectedProposal' => $selectedProposal,
                'lastProposalContent' => $lastProposalContent,
                'chronologicalStream' => $chronologicalStream
            ]);
        }

        $myPendingProposals = $this->getMyPendingProposals();
        $myNotPendingProposals = $this->getMyNotPendingProposals();

        return $this->render('my-proposals',[
            'myPendingProposals' => $myPendingProposals,
            'myNotPendingProposals' => $myNotPendingProposals
        ]);
    }

    /**
     * Check if a Proposal exists. If true it returns the proposal.
     * If false it throws an exception.
     *
     * @param int $id
     * @return Proposal|null
     * @throws \Exception
     */
    private function checkIfProposalExists(int $id): ?Proposal
    {
        $unauthorizedException = NotFoundHttpException::class;

        if (!is_null($selectedProposal = Proposal::findOne(['id' => $id]))) {
            return $selectedProposal;
        }
        throw new $unauthorizedException();
    }

    /**
     * Check if User is owner of the proposal.
     * If true it returns nothing letting the process continue.
     * If false it throws an exception
     *
     * @param int $submitterId
     * @throws Exception
     */
    private function checkIfUserIsOwnerOfProposal(int $submitterId)
    {
        if($submitterId == self::getCurrentUser()->id) {
            return;
        }
        throw new Exception('Not owner of this proposal');
    }

    /**
     * Generates the chronological stream of the proposal
     * by creating an array filled of the different parts
     * of the proposal.
     *
     * @param $comments
     * @param $reviews
     * @param $proposalContentHistories
     * @return array
     */
    private function generateChronologicalStream($comments, $reviews, $proposalContentHistories): array
    {
        $chronologicalStream = array();

        /** @var Comment $comment */
        foreach($comments as $comment) {
            array_push($chronologicalStream, $comment);
        }
        /** @var Review $review */
        foreach($reviews as $review) {
            array_push($chronologicalStream, $review);
        }
        /** @var ProposalContentHistory $history */
        foreach($proposalContentHistories as $history) {
            array_push($chronologicalStream, $history);
        }

        return $chronologicalStream;
    }

    /**
     * Sort the chronological Stream by date
     *
     * @param $chronologicalStream
     * @return mixed
     */
    private function sortChronologicalStreamByDate($chronologicalStream)
    {
        usort($chronologicalStream, function ($a,$b): int {
            $aDate = New \DateTime($a->date);
            $bDate = New \DateTime($b->date);
            if($aDate == $bDate) {
                return 0;
            }
            return ($aDate < $bDate) ? -1 : 1;
        });

        return $chronologicalStream;
    }


    /**
     * Returns the pending proposals of a member.
     *
     * @return ActiveDataProvider
     */
    private function getMyPendingProposals(): ActiveDataProvider
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
                'defaultOrder' => ['date' => SORT_DESC, 'title' => SORT_ASC, 'has_review' => SORT_ASC]
            ]
        ]);

        return $myPendingProposals;
    }

    /**
     * Returns the reviewed proposals of a member.
     *
     * @return ActiveDataProvider
     */
    private function getMyNotPendingProposals(): ActiveDataProvider
    {
        $myReviewedProposals = new ActiveDataProvider([
            'query' => Proposal::find()
                ->where(['not',['status' => 'pending']])
                ->andWhere(['submitter_id' => self::getCurrentUser()->id]),
            'pagination' => [
                'pageSize' => 20,
                'defaultPageSize' => 20
            ],
            'sort' => [
                'attributes' => ['date' ,'title', 'status'],
                'defaultOrder' => ['date' => SORT_DESC, 'title' => SORT_ASC, 'status' => SORT_ASC]
            ]
        ]);

        return $myReviewedProposals;
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
                ->andWhere(['status' => 'pending']),
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
            ->andWhere(['status' => 'pending']),
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