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

        /*$pending = Proposal::find()
            ->join('INNER JOIN', 'review', 'review.proposal_id = proposal.id')
            ->where(['published' => false])
            ->all();
        dd($pending);
        /*foreach($pending as $pend) {
            d($pend->title);
        }*/
//        dd((new Query())->select('proposal_id')->from('review')->column());

        $proposalsDataProvider = new ActiveDataProvider([
//            'query' => (new Query())
//                ->from('proposal')
//                ->innerJoin('review', 'review.proposal_id = proposal.id')
//                ->where(['published' => false]),
            'query' => Proposal::find()
//                ->innerJoinWith('reviews')
                //->where(['published' => false])
                ->andWhere(['in','id',(new Query())->select('proposal_id')->from('review')->column() ]),
            'pagination' => [
                'pageSize' => 20,
                'defaultPageSize' => 20
            ],
            'sort' => [
                'attributes' => ['title'],
                'defaultOrder' => ['title' => SORT_ASC]
            ]
        ]);

        return $this->render('reviewer-pending-proposals', [
            'proposalsDataProvider' => $proposalsDataProvider
        ]);
    }
}