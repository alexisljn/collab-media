<?php


namespace app\controllers;


use app\components\Util;
use app\controllers\mainController\MainController;
use app\models\databaseModels\ProposalFileHistory;
use app\models\exceptions\CannotDeleteFileException;
use app\models\forms\ManageProposalForm;
use app\models\databaseModels\Comment;
use app\models\databaseModels\File;
use app\models\databaseModels\Proposal;
use app\models\databaseModels\ProposalContentHistory;
use app\models\databaseModels\Review;
use app\models\exceptions\CannotHandleUploadedFileException;
use app\models\exceptions\CannotSaveException;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\web\NotFoundHttpException;
use yii;

class ProposalController extends MainController
{
    /**
     * Returns all proposals submitted by a member or a single proposal if
     * an Id was specified in url.
     *
     * @param null|int $id
     * @return string
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
                $selectedProposal->proposalContentHistories,
                $selectedProposal->proposalFileHistories,

            );
            $lastProposalContent = $selectedProposal->proposalContentHistories[
                count($selectedProposal->proposalContentHistories)-1
            ];

            $model = new ManageProposalForm();
            $model->title = $selectedProposal->title;
            $model->content = $lastProposalContent->content;

            return $this->render('my-proposal', [
                'selectedProposal' => $selectedProposal,
                'lastProposalContent' => $lastProposalContent,
                'chronologicalStream' => $chronologicalStream,
                'model' => $model
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
     */
    private function checkIfUserIsOwnerOfProposal(int $submitterId)
    {
        $unauthorizedException = NotFoundHttpException::class;

        if($submitterId == self::getCurrentUser()->id) {
            return;
        }

        throw new $unauthorizedException();
    }

    /**
     * Generates the chronological stream of the proposal
     * by creating an array filled of the different parts
     * of the proposal and sorted them by date.
     *
     * @param $comments
     * @param $reviews
     * @param $proposalContentHistories
     * @return array
     */
    private function generateChronologicalStream($comments, $reviews, $proposalContentHistories, $proposalFileHistories): array
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
        /** @var ProposalFileHistory $fileHistory */
        foreach($proposalFileHistories as $fileHistory) {
            array_push($chronologicalStream, $fileHistory);
        }

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

    /**
     * Display a form to create a Proposal
     * Save the proposal in DB when form is submitted
     *
     * @return string|yii\web\Response
     * @throws \Throwable
     */
    public function actionCreateProposal()
    {
        $model = new ManageProposalForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $transaction = Yii::$app->db->beginTransaction();

            try {
                $postRequest = Yii::$app->request->post();
                $proposal = $this->saveProposal($postRequest['ManageProposalForm']['title']);
                $this->saveProposalContent($postRequest['ManageProposalForm']['content'], $proposal, true);

                if (!empty($_FILES['ManageProposalForm']['name']['relatedFile'])) {
                    $uploadedFile = $_FILES['ManageProposalForm'];
                    $movedFilename = $this->moveUploadedFileToServer($uploadedFile, $proposal);
                    $this->saveProposalRelatedFile($movedFilename, $proposal);
                    $this->saveProposalFileHistory($movedFilename, $proposal, true);
                }

                $transaction->commit();
                return $this->redirect('/proposal/my-proposals/' . $proposal->id);
            } catch (CannotHandleUploadedFileException $cannotHandleUploadedFileException) {
                $transaction->rollBack();
                return $this->render('create-proposal', ['model' => $model, 'error' => 'Invalid file']);

            } catch(\Throwable $e) {
                $transaction->rollBack();
                throw $e;
            }
        } else {
            return $this->render('create-proposal', ['model' => $model, 'error' => null]);
        }
    }

    /**
     * Save proposal in DB
     *
     * @param $proposalTitle
     * @return Proposal
     * @throws CannotSaveException
     */
    private function saveProposal(string $proposalTitle): Proposal
    {
        $proposal = new Proposal();
        $proposal->title = $proposalTitle;
        $proposal->submitter_id = mainController::getCurrentUser()->id;
        $proposal->status = \app\models\Proposal::STATUS_PENDING;
        $proposal->date = Util::getDateTimeFormattedForDatabase(new \DateTime());

        if(!$proposal->save()) {
            throw new CannotSaveException($proposal);
        }

        return $proposal;
    }

    /**
     * Save ProposalContentHistory in DB
     *
     * @param $proposalContent
     * @param $proposal
     * @throws CannotSaveException
     */
    private function saveProposalContent(string $proposalContent,Proposal $proposal, bool $isANewProposal)
    {
        $proposalContentHistory = new ProposalContentHistory();
        $proposalContentHistory->proposal_id = $proposal->id;

        if ($isANewProposal) {
            $proposalContentHistory->date = $proposal->date;
        } else {
            $proposalContentHistory->date = Util::getDateTimeFormattedForDatabase(new \Datetime());
        }

        $proposalContentHistory->content = $proposalContent;

        if(!$proposalContentHistory->save()) {
            throw new CannotSaveException($proposalContentHistory);
        }
    }

    /**
     * Move the uploaded file to server
     *
     * @param $uploadedFile
     * @param $proposal
     * @return string
     * @throws CannotHandleUploadedFileException
     */
    private function moveUploadedFileToServer($uploadedFile, Proposal $proposal): string
    {
        if ($uploadedFile['error']['relatedFile'] != 0) {
            throw new CannotHandleUploadedFileException();
        }

        if ($uploadedFile['size']['relatedFile'] > 52428800) {
            throw new CannotHandleUploadedFileException();
        }

        $explodedFilename = explode('.', $uploadedFile['name']['relatedFile']);
        $extension = $explodedFilename[count($explodedFilename)-1];

        if (!in_array($extension,Util::UPLOADED_FILE_ALLOWED_EXTENSIONS)) {
            throw new CannotHandleUploadedFileException();
        }

        $newFilename = basename($proposal->id . '.' . $extension);
        if(!move_uploaded_file(
            $uploadedFile['tmp_name']['relatedFile'],
            '../uploaded-files/proposal-related-files/' . $newFilename
        )) {
            throw new CannotHandleUploadedFileException();
        }

        return $newFilename;
    }

    /**
     * Save the file in DB.
     *
     * @param $movedFilename
     * @param $proposal
     * @throws CannotSaveException
     */
    private function saveProposalRelatedFile(string $movedFilename, Proposal $proposal)
    {
        $file = new File();
        $file->proposal_id = $proposal->id;
        $file->path = $movedFilename;

        if (!$file->save()) {
            throw new CannotSaveException($file);
        }
    }

    /**
     * Save the proposal file history in DB.
     *
     * @param string $movedFilename
     * @param Proposal $proposal
     * @param bool $isANewProposal
     * @throws CannotSaveException
     */
    private function saveProposalFileHistory(string $movedFilename, Proposal $proposal, bool $isANewProposal)
    {
        $proposalFileHistory = new ProposalFileHistory();
        $proposalFileHistory->proposal_id = $proposal->id;
        $proposalFileHistory->path = $movedFilename;
        if($isANewProposal) {
            $proposalFileHistory->date = $proposal->date;
        }
        $proposalFileHistory->date = Util::getDateTimeFormattedForDatabase(new \DateTime());

        if (!$proposalFileHistory->save()) {
            throw new CannotSaveException($proposalFileHistory);
        }
    }

    /**
     * Allow a user to edit his proposal. It loads a form
     * which is displayed if the user click on edit link
     *
     * @return yii\web\Response
     * @throws \Throwable
     */
    public function actionEditProposal()
    {
        $editedProposal = Proposal::findOne(['id' => Yii::$app->request->get()]);
        $lastProposalContent = $editedProposal->proposalContentHistories[
        count($editedProposal->proposalContentHistories)-1];
        $model = New ManageProposalForm();
        $postRequest = Yii::$app->request->post();

        $model->title = $postRequest['ManageProposalForm']['title'];
        $model->content = $postRequest['ManageProposalForm']['content'];

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $this->saveEditedProposal($postRequest['ManageProposalForm']['title'], $editedProposal);

                if ($postRequest['ManageProposalForm']['content'] != $lastProposalContent->content) {
                    $this->saveProposalContent($postRequest['ManageProposalForm']['content'], $editedProposal, false);
                }

                if (!empty($_FILES['ManageProposalForm']['name']['relatedFile'])) {

                    if (!is_null($editedProposal->files[0]->path)) {
                        $this->removeExistingUploadedFile($editedProposal->files[0]->path);
                    }

                    $uploadedFile = $_FILES['ManageProposalForm'];
                    $movedFilename = $this->moveUploadedFileToServer($uploadedFile, $editedProposal);
                    $this->saveEditedFile($movedFilename, $editedProposal);
                    $this->saveProposalFileHistory($movedFilename, $editedProposal, false);
                }

                $transaction->commit();
                return $this->redirect('/proposal/my-proposals/' . $editedProposal->id);
            } catch (CannotHandleUploadedFileException $cannotHandleUploadedFileException) {
                $transaction->rollBack();
                return $this->redirect('/proposal/my-proposals/' . $editedProposal->id);

            } catch(\Throwable $e) {
                $transaction->rollBack();
                throw $e;
            }
        } else {
            return $this->redirect('/proposal/my-proposals/' . $editedProposal->id);
        }
    }

    /**
     * Save the edited Proposal in DB.
     *
     * @param string $proposalTitle
     * @param Proposal $proposal
     * @throws CannotSaveException
     */
    private function saveEditedProposal(string $proposalTitle, Proposal $proposal)
    {
        $proposal->title = $proposalTitle;

        if(!$proposal->save()) {
            throw new CannotSaveException($proposal);
        }
    }

    /**
     * Save the edited file in DB
     *
     * @param $movedFilename
     * @param Proposal $proposal
     * @throws CannotSaveException
     */
    private function saveEditedFile($movedFilename, Proposal $proposal)
    {
        $file = $proposal->files[0];
        $file->path = $movedFilename;

        if (!$file->save()) {
            throw new CannotSaveException($file);
        }
    }

    /**
     * Remove the existing uploaded file on server
     *
     * @param string $filepath
     * @throws CannotDeleteFileException
     */
    private function removeExistingUploadedFile(string $filepath)
    {
        if (!unlink('../uploaded-files/proposal-related-files/'. $filepath)) {
            throw new CannotDeleteFileException();
        }
    }
}