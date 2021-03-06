<?php


namespace app\controllers;


use app\components\socialMediaApi\TwitterConnector;
use app\components\Util;
use app\controllers\mainController\MainController;
use app\models\databaseModels\EnabledSocialMedia;
use app\models\databaseModels\ProposalFileHistory;
use app\models\databaseModels\SocialMediaPermission;
use app\models\exceptions\CannotDeleteFileException;
use app\models\exceptions\FileDoesNotExistException;
use app\models\exceptions\TwitterAPIException;
use app\models\forms\ManageCommentForm;
use app\models\forms\ManageProposalForm;
use app\models\databaseModels\Comment;
use app\models\databaseModels\File;
use app\models\databaseModels\Proposal;
use app\models\databaseModels\ProposalContentHistory;
use app\models\databaseModels\Review;
use app\models\exceptions\CannotHandleUploadedFileException;
use app\models\exceptions\CannotSaveException;
use app\models\forms\PublishProposalForm;
use app\models\ProposalApprovementSetting;
use app\models\User;
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
     * @return string
     */
    public function actionMyProposals(): string
    {
        $myPendingProposals = $this->buildMyPendingProposalsActiveDataProvider();
        $myNotPendingProposals = $this->buildMyNotPendingProposalsActiveDataProvider();

        return $this->render('my-proposals',[
            'myPendingProposals' => $myPendingProposals,
            'myNotPendingProposals' => $myNotPendingProposals
        ]);
    }

    public function actionProposal(int $id)
    {
        $selectedProposal = $this->checkIfProposalExists($id);
        $viewItems = $this->buildOneProposalViewItems($selectedProposal);

        switch (MainController::getCurrentUser()->role) {
            case User::USER_ROLE_MEMBER:
                $this->checkIfUserCanSeeProposal($selectedProposal->submitter_id);
                break;
            case User::USER_ROLE_REVIEWER:
                break;
             /** @TODO USER_ROLE_PUBLISHER & ADMIN */

        }
        return $this->render('proposal', $viewItems);
    }

    /**
     * Build needed Items to be displayed in single
     * proposal consultation.
     *
     * @param Proposal $selectedProposal
     * @return array
     */
    private function buildOneProposalViewItems(Proposal $selectedProposal)
    {
        $chronologicalStream = $this->generateChronologicalStream
        (
            $selectedProposal->comments,
            $selectedProposal->reviews,
            $selectedProposal->proposalContentHistories,
            $selectedProposal->proposalFileHistories,

        );
        $lastProposalContent = ProposalContentHistory::find()
            ->where(['proposal_id' => $selectedProposal->id])
            ->orderBy('date DESC')
            ->limit(1)
            ->one();

        $approvalsCount = Review::find()->where(['proposal_id' => $selectedProposal->id])
            ->andWhere(['status' => \app\models\Review::REVIEW_STATUS_APPROVED])
            ->count();
        $disapprovalsCount = Review::find()->where(['proposal_id' => $selectedProposal->id])
            ->andWhere(['status' => \app\models\Review::REVIEW_STATUS_DISAPPROVED])
            ->count();

        $manageProposalFormModel = new ManageProposalForm();
        $manageProposalFormModel->title = $selectedProposal->title;
        $manageProposalFormModel->content = $lastProposalContent->content;
        $manageCommentFormModel = new ManageCommentForm();
        $canEditProposal = $this->canEditProposal($selectedProposal);
        $canPublishProposal = $this->canPublishProposal($selectedProposal);

        $viewItems = [
            'selectedProposal' => $selectedProposal,
            'lastProposalContent' => $lastProposalContent,
            'chronologicalStream' => $chronologicalStream,
            'approvalsCount' => $approvalsCount,
            'disapprovalsCount' => $disapprovalsCount,
            'manageProposalFormModel' => $manageProposalFormModel,
            'manageCommentFormModel' => $manageCommentFormModel,
            'canEditProposal' => $canEditProposal,
            'canPublishProposal' => $canPublishProposal
        ];

      /*  if (!is_null($selectedProposal->file))
        {
           $this->getRelatedFile($selectedProposal->file->path);
        }*/

        if ($this->checkIfReviewerCanReviewProposal($selectedProposal)) {
            $potentialReview = $this->getPotentialReviewOfAReviewer
            (
                $selectedProposal,
                MainController::getCurrentUser()->id
            );
            $viewItems['potentialReview'] = $potentialReview;
        }

        return $viewItems;
    }


    /**
     * Check if a Proposal exists. If true it returns the proposal.
     * If false it throws an exception.
     *
     * @param int $id
     * @return \app\models\Proposal|null
     */
    private function checkIfProposalExists(int $id): ?\app\models\Proposal
    {
        $notFoundException = NotFoundHttpException::class;

        if (!is_null($selectedProposal = \app\models\Proposal::findOne(['id' => $id]))) {
            return $selectedProposal;
        }

        throw new $notFoundException();
    }

    public function actionGetFile(int $id)
    {
        $selectedProposal = $this->checkIfProposalExists($id);
        $this->checkIfUserCanSeeProposal($selectedProposal->submitter_id);
        $filepath = '../uploaded-files/proposal-related-files/' . $selectedProposal->file->path;

        if (!file_exists($filepath)) {
            throw new FileDoesNotExistException();
        }

        header('Content-Type: ' . mime_content_type(($filepath)));
        echo file_get_contents($filepath);
        die;
    }

    /**
     * Check if User can see the proposal.
     * If true it returns nothing letting the process continue.
     * If false it throws an exception
     *
     * @param int $submitterId
     */
    private function checkIfUserCanSeeProposal(int $submitterId)
    {
        $unauthorizedException = NotFoundHttpException::class;

        if($submitterId == self::getCurrentUser()->id
            || MainController::getCurrentUser()->role != User::USER_ROLE_MEMBER) {
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
    private function buildMyPendingProposalsActiveDataProvider(): ActiveDataProvider
    {
        return new ActiveDataProvider([
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
                'pageParam' => 'pendingProposals',
                'pageSize' => 20,
                'defaultPageSize' => 20
            ],
            'sort' => [
                'attributes' => ['date' ,'title', 'has_review'],
                'defaultOrder' => ['date' => SORT_DESC, 'title' => SORT_ASC, 'has_review' => SORT_ASC]
            ]
        ]);
    }

    /**
     * Returns the reviewed proposals of a member.
     *
     * @return ActiveDataProvider
     */
    private function buildMyNotPendingProposalsActiveDataProvider(): ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => Proposal::find()
                ->where(['not',['status' => \app\models\Proposal::STATUS_PENDING]])
                ->andWhere(['submitter_id' => self::getCurrentUser()->id]),
            'pagination' => [
                'pageParam' => 'historyProposals',
                'pageSize' => 20,
                'defaultPageSize' => 20
            ],
            'sort' => [
                'attributes' => ['date' ,'title', 'status'],
                'defaultOrder' => ['date' => SORT_DESC, 'title' => SORT_ASC, 'status' => SORT_ASC]
            ]
        ]);
    }

    /**
     * Returns proposals that can be access by a rewiewer depending his activity
     *
     * @return string
     */
    public function actionReviewerPendingProposals(): string
    {
        $proposalsToReviewActiveDataProvider = $this->buildProposalsToReviewActiveDataProvider();
        $reviewedProposalsActiveDataProvider = $this->buildReviewedProposalsActiveDataProvider();

        return $this->render('reviewer-pending-proposals', [
            'proposalsToReviewActiveDataProvider' => $proposalsToReviewActiveDataProvider,
            'reviewedProposalsActiveDataProvider' => $reviewedProposalsActiveDataProvider
        ]);
    }

    /**
     * Returns proposals not reviewed and not published for a reviewer
     *
     * @return ActiveDataProvider
     */
    private function buildProposalsToReviewActiveDataProvider(): ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => Proposal::find()
               ->where([
                    'not in',
                    'id',
                    (new Query())
                        ->select('proposal_id')
                        ->from('review')
                        ->where(['reviewer_id' => self::getCurrentUser()->id])
                        ->andWhere(['not', ['status' => \app\models\Review::REVIEW_STATUS_CANCELLED]])
                ])
                ->andWhere(['status' => 'pending']),
            'pagination' => [
                'pageParam' => 'noReviewed',
                'pageSize' => 20,
                'defaultPageSize' => 20
            ],
            'sort' => [
                'sortParam' => 'pendingSort',
                'attributes' => ['date', 'title'],
                'defaultOrder' => [
                    'date' => SORT_DESC
                ]
            ]
        ]);
    }

    /**
     * Returns proposals already reviewed but not published for a reviewer
     *
     * @return ActiveDataProvider
     */
    private function buildReviewedProposalsActiveDataProvider(): ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => Proposal::find()
            ->where([
                'in',
                'id',
                (new Query())
                    ->select('proposal_id')
                    ->from('review')
                    ->where(['reviewer_id' => self::getCurrentUser()->id])
                    ->andWhere(['not', ['status' => \app\models\Review::REVIEW_STATUS_CANCELLED]])
            ])
            ->andWhere(['status' => 'pending']),
            'pagination' => [
                'pageParam' => 'reviewed',
                'pageSize' => 20,
                'defaultPageSize' => 20
            ],
            'sort' => [
                'sortParam' => 'historySort',
                'attributes' => ['date', 'title'],
                'defaultOrder' => [
                    'date' => SORT_DESC,
                    'title' => SORT_ASC
                ]
            ]
        ]);
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

                return $this->redirect('/proposal/proposal/' . $proposal->id);
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

        $explodedFilename = explode('.', $uploadedFile['name']['relatedFile']);
        $extension = $explodedFilename[count($explodedFilename)-1];

        if (!array_key_exists($extension,Util::UPLOADED_FILE_RULES)) {
            throw new CannotHandleUploadedFileException();
        }

        if ($uploadedFile['size']['relatedFile'] > Util::UPLOADED_FILE_RULES[$extension]) {
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
        } else {
            $proposalFileHistory->date = Util::getDateTimeFormattedForDatabase(new \DateTime());
        }


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

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $this->saveEditedProposal($model->title, $editedProposal);

                if ($model->content != $lastProposalContent->content) {
                    $this->saveProposalContent($model->content, $editedProposal, false);
                }

                if (!empty($_FILES['ManageProposalForm']['name']['relatedFile'])) {

                    if (!is_null($editedProposal->file->path)) {
                        $this->removeExistingUploadedFile($editedProposal->file->path);
                    }

                    $uploadedFile = $_FILES['ManageProposalForm'];
                    $movedFilename = $this->moveUploadedFileToServer($uploadedFile, $editedProposal);

                    if (!is_null($editedProposal->file)) {
                        $this->saveEditedFile($movedFilename, $editedProposal);
                    } else {
                        $this->saveProposalRelatedFile($movedFilename, $editedProposal);
                    }

                    $this->saveProposalFileHistory($movedFilename, $editedProposal, false);
                }

                $transaction->commit();

                return $this->redirect('/proposal/proposal/' . $editedProposal->id);
            } catch (CannotHandleUploadedFileException $cannotHandleUploadedFileException) {
                $transaction->rollBack();
                return $this->redirect('/proposal/proposal/' . $editedProposal->id);
            } catch(\Throwable $e) {
                $transaction->rollBack();
                throw $e;
            }
        } else {
            return $this->redirect('/proposal/proposal/' . $editedProposal->id);
        }
    }

    private function canEditProposal(Proposal $proposal)
    {
        if (MainController::getCurrentUser()->id === $proposal->submitter_id
            && $proposal->status === \app\models\Proposal::STATUS_PENDING) {
            return true;
        }

        return false;
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
        $file = $proposal->file;
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

    /**
     * Verify if user can comment a proposal
     * then call the saveComment method
     *
     * @return yii\web\Response
     * @throws CannotSaveException
     */
    public function actionPostComment()
    {
        $model = new ManageCommentForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $proposalId = Yii::$app->request->get()['id'];
            $this->checkIfUserCanSeeProposal($this->checkIfProposalExists($proposalId)->submitter_id);
            $this->saveComment($model->content, $proposalId);
        }

        return $this->redirect('/proposal/proposal/'. $proposalId);
    }

    /**
     * Create a form populated by user's input then
     * it checks if user is allowed to edit a comment
     * and save it if true.
     *
     * @return yii\web\Response
     * @throws CannotSaveException
     */
    public function actionEditComment(int $id)
    {
        $model = new ManageCommentForm();
        $model->id = Yii::$app->request->post()['ManageCommentForm']['id'];
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $supposedCommentId = $model->id;
            $originalComment = $this->checkIfCommentExists($supposedCommentId);
            $this->checkIfUserIsOwnerOfComment($originalComment);
            $this->checkIfCommentIsFromCurrentProposal($originalComment, $id);
            $this->checkIfUserCanSeeProposal($this->checkIfProposalExists($originalComment->proposal_id)->submitter_id);
            $this->saveEditedComment($originalComment, $model->content);
        }

        return $this->redirect('/proposal/proposal/' . $originalComment->proposal_id);
    }


    /**
     * Create a new comment in DB.
     *
     * @param string $commentInput
     * @param int $proposalId
     * @throws CannotSaveException
     */
    private function saveComment(string $commentInput, int $proposalId)
    {
        $comment = new Comment();
        $comment->proposal_id = $proposalId;
        $comment->content = $commentInput;
        $comment->author_id = MainController::getCurrentUser()->id;
        $comment->date = Util::getDateTimeFormattedForDatabase(new \DateTime());

        if (!$comment->save()) {
            throw new CannotSaveException($comment);
        }
    }

    /**
     * Check if a comment exists in DB.
     *
     * @param int $commentId
     * @return Comment
     */
    private function checkIfCommentExists(int $commentId): Comment
    {
        $notFoundException = NotFoundHttpException::class;

        if(is_null($comment = Comment::findOne(['id' => $commentId]))) {
            throw new $notFoundException();
        }

        return $comment;
    }

    /**
     * Check if User is owner of a comment.
     * An admin can edit any comment.
     *
     * @param Comment $comment
     */
    private function checkIfUserIsOwnerOfComment(Comment $comment)
    {
        $unauthorizedException = NotFoundHttpException::class;

        if (MainController::getCurrentUser()->role === User::USER_ROLE_ADMIN) {
            return;
        }

        if(MainController::getCurrentUser()->id != $comment->author_id) {
            throw new $unauthorizedException();
        }
    }

    /**
     * Check if a comment is part of one proposal's comments.
     * It checks if the user doesn't try to edit a comment coming
     * from another proposal
     *
     * @param Comment $comment
     */
    private function checkIfCommentIsFromCurrentProposal(Comment $comment, int $proposalId)
    {
        $unauthorizedException = NotFoundHttpException::class;

            if($comment->proposal->id == $proposalId) {
                return;
            }

        throw new $unauthorizedException();
    }

    /**
     * Populate the edited comment of its new content
     * then save it in DB.
     *
     * @param Comment $comment
     * @param $newCommentContent
     * @throws CannotSaveException
     */
    private function saveEditedComment(Comment $comment, $newCommentContent)
    {
        $comment->content = $newCommentContent;
        $comment->edited_date = Util::getDateTimeFormattedForDatabase(new \DateTime());

        if (!$comment->save()) {
            throw new CannotSaveException($comment);
        }
    }

    /**
     * Display proposals for a Publisher
     * or single proposal
     *
     * @return string
     */
    public function actionDashboard()
    {
        $proposalsCount = Proposal::find()->count();
        $publishedProposalsCount = Proposal::find()
            ->where(['status' => \app\models\Proposal::STATUS_PUBLISHED])
            ->count();
        $pendingProposalsCount = Proposal::find()
            ->where(['status' => \app\models\Proposal::STATUS_PENDING])
            ->count();
        $rejectedProposalsCount = Proposal::find()
            ->where(['status' => \app\models\Proposal::STATUS_REJECTED])
            ->count();
        $proposalsReviewedByUserCount = Review::find()
            ->where(['reviewer_id' => MainController::getCurrentUser()->id])
            ->count();
        $proposalsCreatedByUserCount = Proposal::find()
            ->where(['submitter_id' => MainController::getCurrentUser()->id])
            ->count();
        $userProposalsPublishedCount = Proposal::find()
            ->where(['submitter_id' => MainController::getCurrentUser()->id])
            ->andWhere(['status' => \app\models\Proposal::STATUS_PUBLISHED])
            ->count();

        $approvedProposalsQuery = $this->buildApprovedProposalsQuery();
        $approvedProposals = $this->buildApprovedProposalsActiveDataProvider($approvedProposalsQuery);
        $notApprovedProposals = $this->buildNotApprovedProposalsActiveDataProvider($approvedProposalsQuery);
        $publishedProposals = $this->buildPublishedProposalsActiveDataProvider();
        $rejectedProposals = $this->buildRejectedProposalsActiveDataProvider();

        return $this->render('dashboard', [
            'proposalsCount' => $proposalsCount,
            'publishedProposalsCount' => $publishedProposalsCount,
            'pendingProposalsCount' => $pendingProposalsCount,
            'rejectedProposalsCount' => $rejectedProposalsCount,
            'proposalsReviewedByUserCount' => $proposalsReviewedByUserCount,
            'proposalsCreatedByUserCount' => $proposalsCreatedByUserCount,
            'userProposalsPublishedCount' => $userProposalsPublishedCount,
            'approvedProposals' => $approvedProposals,
            'notApprovedProposals' => $notApprovedProposals,
            'publishedProposals' => $publishedProposals,
            'rejectedProposals' => $rejectedProposals
        ]);
    }

    /**
     * Build the query for retreive approved proposals
     * and returns it without execution, to pass
     * it into an ActiveDataProvider
     *
     * @return yii\db\ActiveQuery
     */
    private function buildApprovedProposalsQuery()
    {
        return \app\models\Proposal::find()
            ->select('proposal.*, (SELECT COUNT(*) FROM review WHERE review.proposal_id = proposal.id) as count_reviews')
            ->where('1 = CASE 
                WHEN 
                    ((((SELECT COUNT(*) FROM review 
                        WHERE review.status = \'approved\' AND review.proposal_id = proposal.id)
                        /
                        (SELECT COUNT(*) FROM review
                        WHERE review.proposal_id = proposal.id))
                    *100)  
                    >= ' . yii\helpers\Html::encode(ProposalApprovementSetting::getApprovementPercent()). ') 
                    THEN 1 
                ELSE 0 
                END')
            ->andWhere(['proposal.status' => \app\models\Proposal::STATUS_PENDING])
            ->andWhere('1 = CASE
                WHEN
                    ((SELECT COUNT(*) FROM review
                    WHERE review.proposal_id = proposal.id)
                    >= ' . yii\helpers\Html::encode(ProposalApprovementSetting::getRequiredNumberOfReview()) . ')
                    THEN 1
                ELSE 0
                END');
    }

    /**
     * Returns an ActiveDataProvider for the
     * approved proposals.
     *
     * @param yii\db\ActiveQuery $approvedProposalsQuery
     * @return ActiveDataProvider
     */
    private function buildApprovedProposalsActiveDataProvider(yii\db\ActiveQuery $approvedProposalsQuery)
    {
        return new ActiveDataProvider([
            'query' => $approvedProposalsQuery,
            'pagination' => [
                'pageParam' => 'approvedProposals',
                'pageSize' => 20,
                'defaultPageSize' => 20
            ],
            'sort' => [
                'sortParam' => 'approvedSort',
                'attributes' => ['count_reviews', 'date', 'title'],
                'defaultOrder' => [
                    'count_reviews' => SORT_DESC,
                    'date' => SORT_DESC,
                    'title' => SORT_ASC
                ]
            ]
        ]);
    }

    /**
     * Returns an ActiveDataProvider for the not
     * approved proposals by using the approvedProposalsQuery
     *
     * @param yii\db\ActiveQuery $approvedProposalsQuery
     * @return ActiveDataProvider
     */
    private function buildNotApprovedProposalsActiveDataProvider(yii\db\ActiveQuery$approvedProposalsQuery)
    {
        $approvedProposals = $approvedProposalsQuery->all();
        $approvedProposalsId = array();

        /** @var Proposal $approvedProposal */
        foreach ($approvedProposals as $approvedProposal) {
                array_push($approvedProposalsId, $approvedProposal->id);
        }

        return new ActiveDataProvider([
            'query' => \app\models\Proposal::find()
                ->select('proposal.*, (SELECT COUNT(*) FROM review WHERE review.proposal_id = proposal.id) as count_reviews')
                ->where([
                    'not in', 'id', $approvedProposalsId
                ])
                ->andWhere(['status' => \app\models\Proposal::STATUS_PENDING]),
            'pagination' => [
                'pageParam' => 'notApprovedProposals',
                'pageSize' => 20,
                'defaultPageSize' => 20
            ],
            'sort' => [
                'sortParam' => 'notApprovedSort',
                'attributes' => ['count_reviews', 'date', 'title'],
                'defaultOrder' => [
                    'count_reviews' => SORT_DESC,
                    'date' => SORT_DESC,
                    'title' => SORT_ASC
                ]
            ]
        ]);
    }

    /**
     * Builds published proposals's ActiveDateProvider
     *
     * @return ActiveDataProvider
     */
    private function buildPublishedProposalsActiveDataProvider()
    {
        return new ActiveDataProvider([
            'query' => Proposal::find()->where(['status' => \app\models\Proposal::STATUS_PUBLISHED]),
            'pagination' => [
                'pageParam' => 'publishedProposals',
                'pageSize' => 20,
                'defaultPageSize' => 20
            ],
            'sort' => [
                'sortParam' => 'publishedSort',
                'attributes' => ['date', 'title'],
                'defaultOrder' => [
                    'date' => SORT_DESC,
                    'title' => SORT_ASC
                ]
            ]
        ]);
    }

    /**
     * Builds the ActiveDataProvider for the rejected proposals
     *
     * @return ActiveDataProvider
     */
    private function buildRejectedProposalsActiveDataProvider()
    {
        return new ActiveDataProvider([
            'query' => Proposal::find()->where(['status' => \app\models\Proposal::STATUS_REJECTED]),
            'pagination' => [
                'pageParam' => 'rejectedProposals',
                'pageSize' => 20,
                'defaultPageSize' => 20
            ],
            'sort' => [
                'sortParam' => 'publishedSort',
                'attributes' => ['date', 'title'],
                'defaultOrder' => [
                    'date' => SORT_DESC,
                    'title' => SORT_ASC
                ]
            ]
        ]);
    }

    /**
     * Check if reviewer cans review proposal based on the proposal status
     * and submitter.
     *
     * @param Proposal $proposal
     * @return bool
     */
    private function checkIfReviewerCanReviewProposal(Proposal $proposal)
    {
        if (MainController::getCurrentUser()->id !== $proposal->submitter_id
            && $proposal->status === \app\models\Proposal::STATUS_PENDING) {
            return true;
        }

        return false;
    }

    /**
     * Returns the current review of a user for a proposal
     * or false if he hasn't reviewed it yet.
     *
     * @param \app\models\Proposal $proposal
     * @param $reviewerId
     * @return Review|array|bool|yii\db\ActiveRecord|null
     */
    private function getPotentialReviewOfAReviewer(\app\models\Proposal $proposal, $reviewerId)
    {

        if (!is_null($review = Review::find()
            ->where(['proposal_id' => $proposal->id])
            ->andWhere(['reviewer_id' => $reviewerId])
            ->one())) {

            return $review;
        }

        return false;
    }

    /**
     * Call method to save Review in DB
     * and call method to generate view items refresh.
     *
     * @return false|string
     * @throws CannotSaveException
     */
    public function actionPostReview()
    {
       $selectedReview = null;
       $reviewStatus = Yii::$app->request->post()['reviewStatus'];
       $proposalId = Yii::$app->request->post()['proposalId'];

        if (!is_null(Yii::$app->request->post()['reviewId'])) {
            $selectedReview = $this->checkIfReviewExists(Yii::$app->request->post()['reviewId']);
            $this->checkIfUserIsOwnerOfReview($selectedReview, MainController::getCurrentUser()->id);
            $this->checkIfReviewIsFromCurrentProposal($selectedReview, $proposalId);
        }

        $review = $this->saveReview($selectedReview, $reviewStatus, MainController::getCurrentUser()->id, $proposalId);
        $ajaxResponse = array('reviewId' => $review->id, 'html' => $this->actionProposal($proposalId));
        $ajaxResponse = json_encode($ajaxResponse);

        return $ajaxResponse;
    }

    /**
     * Check if Review exists then returns it if true.
     *
     * @param $reviewId
     * @return \app\models\Review
     */
    private function checkIfReviewExists($reviewId): \app\models\Review
    {
        $notFoundException = NotFoundHttpException::class;

        if (!is_null($selectedReview = \app\models\Review::findOne(['id' => $reviewId]))) {
            return $selectedReview;
        }

        throw new $notFoundException();
    }

    /**
     * Check if a user is owner of a Review
     *
     * @param \app\models\Review $review
     * @param $userId
     */
    private function checkIfUserIsOwnerOfReview(\app\models\Review $review, $userId)
    {
        $unauthorizedException = NotFoundHttpException::class;

        if ($review->reviewer_id !== $userId) {
            throw new $unauthorizedException();
        }
    }

    /**
     * Check if Review is from current Proposal
     *
     * @param \app\models\Review $review
     * @param $proposalId
     */
    private function checkIfReviewIsFromCurrentProposal(\app\models\Review $review, $proposalId)
    {
        $unauthorizedException = NotFoundHttpException::class;

        if ($review->proposal_id !== (int) $proposalId)
        {
            throw new $unauthorizedException();
        }
    }

    /**
     * Save in DB new Review or use
     * saveEditedReview() if Review is
     * already existing.
     *
     * @param \app\models\Review|null $currentReview
     * @param $reviewStatus
     * @param $reviewerId
     * @param $proposalId
     * @return \app\models\Review
     * @throws CannotSaveException
     */
    private function saveReview(?\app\models\Review $currentReview, $reviewStatus, $reviewerId, $proposalId)
    {

        if (!is_null($currentReview)) {

            $editedReview = $this->saveEditedReview($currentReview, $reviewStatus);
            return $editedReview;
        }

        $review = new \app\models\Review();
        $review->reviewer_id = $reviewerId;
        $review->proposal_id = $proposalId;
        $review->date = Util::getDateTimeFormattedForDatabase(new \DateTime());
        $review->status = $reviewStatus;

        if (!$review->save()) {
            throw new CannotSaveException($review);
        }

        return $review;
    }

    /**
     * Save in DB the edited Review.
     *
     * @param \app\models\Review $review
     * @param $reviewStatus
     * @return \app\models\Review
     * @throws CannotSaveException
     */
    private function saveEditedReview(\app\models\Review $review, $reviewStatus)
    {
        $review->status = $reviewStatus;
        $review->date = Util::getDateTimeFormattedForDatabase(new \DateTime());

        if (!$review->save()) {
            throw new CannotSaveException($review);
        }

        return $review;
    }

    /**
     * Check if user can publish Proposal
     *
     * @param Proposal $proposal
     * @return bool
     */
    private function canPublishProposal(Proposal $proposal)
    {
        $currentUser = MainController::getCurrentUser();

        if (($currentUser->hasRole(User::USER_ROLE_PUBLISHER)) && ($proposal->submitter_id !== $currentUser->id)
            && $proposal->status === \app\models\Proposal::STATUS_PENDING) {
            return true;
        }

        return false;
    }

    /**
     * Pass pending proposal's status to rejected
     *
     * @return string
     * @throws CannotSaveException
     */
    public function actionRejectProposal()
    {
        $proposalId = Yii::$app->request->post()['proposalId'];
        $selectedProposal = $this->checkIfProposalExists($proposalId);
        $selectedProposal->status = \app\models\Proposal::STATUS_REJECTED;

        if (!$selectedProposal->save()) {
            throw new CannotSaveException($selectedProposal);
        }

        return $this->actionProposal($proposalId);
    }

    /**
     * Display the publishing form of a proposal
     * If post loaded, it tweets the proposal and
     * save proposal's status to published
     *
     * @param int $id
     * @return string
     * @throws \Throwable
     */
    public function actionPublishProposal(int $id)
    {
        $selectedProposal = $this->checkIfProposalExists($id);
        $publishProposalFormModel = new PublishProposalForm();

        if ($publishProposalFormModel->load(Yii::$app->request->post()) && $publishProposalFormModel->validate()) {
            $twitterConnector = new TwitterConnector();
            $transaction = Yii::$app->db->beginTransaction();

            try {
                $selectedProposal->status = \app\models\Proposal::STATUS_PUBLISHED;
                $selectedProposal->social_media = "twitter";

                if ($publishProposalFormModel->file) {
                    $twitterConnector->addMedia('../uploaded-files/proposal-related-files/'. $selectedProposal->file->path);
                }

                $response = $twitterConnector->postTweet($publishProposalFormModel->content);

                if (preg_match('/^2/', $response['statusCode']) !== 1) {
                    throw new TwitterAPIException();
                }

                if (!$selectedProposal->save()) {
                    throw new CannotSaveException($selectedProposal);
                }

                $transaction->commit();

                return $this->redirect('/proposal/proposal/' . $selectedProposal->id);
            } catch (\Throwable $e) {
                $transaction->rollBack();
                throw $e;
            }
        }

        $enabledSocialMedia = EnabledSocialMedia::find()->where(['is_enabled' => true])->all();
        $socialMediaPermission = SocialMediaPermission::findOne(['publisher_id' => MainController::getCurrentUser()->id]);
        $allowedSocialMedia = $this->getAllowedPermissionsForPublisher($enabledSocialMedia, $socialMediaPermission);
        $selectedProposal = $this->checkIfProposalExists($id);
        $publishProposalFormModel->content = strip_tags((new \Parsedown())
            ->text($selectedProposal->proposalContentHistories
            [
            count($selectedProposal->proposalContentHistories) -1
            ]->content));

        return $this->render('publish-proposal', [
            'publishProposalFormModel' => $publishProposalFormModel,
            'selectedProposal' => $selectedProposal,
            'allowedSocialMedia' => $allowedSocialMedia
        ]);


    }

    /**
     * Returns allowed publishing social media for a publisher.
     *
     * @param array $enabledSocialMedia
     * @param SocialMediaPermission $socialMediaPermission
     * @return null
     */
    private function getAllowedPermissionsForPublisher(array $enabledSocialMedia, SocialMediaPermission $socialMediaPermission)
    {
        $allowedSocialMedia = null;

        foreach ($enabledSocialMedia as $socialMedia) {

            foreach (array_keys($socialMediaPermission->attributeLabels()) as $key) {

                if (stristr($key, $socialMedia->social_media_name)) {

                    if ($socialMediaPermission->$key) {
                        $allowedSocialMedia[$socialMedia->social_media_name] = ucfirst($socialMedia->social_media_name);
                    }
                }
            }

        }

        return $allowedSocialMedia;
    }

}