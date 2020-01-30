<?php


namespace app\controllers;


use app\models\databaseModels\Comment;
use app\models\databaseModels\Proposal;
use app\models\databaseModels\ProposalContentHistory;
use app\models\databaseModels\Review;
use app\models\databaseModels\User;
use yii\db\Exception;
use yii\web\Controller;

class DevController extends Controller
{
    static function getLorem(int $minLength, int $maxLength) {
        $lorem = explode(' ', str_replace('.', '', str_replace(',', '', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce nec feugiat orci, ac vulputate augue. Proin mattis sed velit sodales congue. Donec eu leo nisi. Morbi pulvinar dolor quis ultrices iaculis. Phasellus ipsum mi, laoreet auctor urna sed, congue ultricies elit. Nunc vel ante nec nunc accumsan pretium. Donec tempus laoreet sapien, consequat ultricies ipsum facilisis nec. Mauris vel neque gravida, egestas dui vitae, rhoncus ipsum. Cras nec est pellentesque, viverra erat in, ornare augue. Interdum et malesuada fames ac ante ipsum primis in faucibus. Quisque vel libero non arcu laoreet vestibulum. In vel fermentum sem, vitae interdum arcu. Pellentesque mattis iaculis volutpat. Vestibulum suscipit consequat mauris vel pellentesque. Nullam lacinia lectus vitae posuere maximus. Mauris tortor odio, commodo tempus pulvinar in, condimentum in lorem. Integer congue purus a libero accumsan, et elementum odio maximus. Donec condimentum ac risus vitae pharetra. Fusce aliquam risus elit, laoreet viverra lacus commodo at. Donec eu blandit diam. Ut eget sapien egestas, dapibus elit at, scelerisque nunc. Suspendisse neque quam, ornare non tempor a, placerat quis magna. Quisque tincidunt quam id molestie sagittis. Fusce neque ligula, vestibulum eu risus quis, semper imperdiet nulla. Proin feugiat magna at nulla varius ornare. Nullam quis dui tempor, elementum dolor vel, posuere est. Etiam blandit odio at mi finibus, eget tristique nisi dignissim. Phasellus fringilla, nisi sit amet interdum molestie, diam ante venenatis nisi, et luctus nisi urna at lectus. Aenean ac dui non erat tempus interdum eu eget elit. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Pellentesque pulvinar risus lorem, nec luctus mi tempor quis. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Proin augue justo, porttitor in rutrum accumsan, fermentum vel magna. Ut a justo mattis, consequat felis et, porttitor felis. Vivamus at facilisis ex. Fusce in risus tincidunt, tempor nibh id, varius elit. Morbi lobortis ultrices ullamcorper. Donec a massa efficitur, auctor tellus at, maximus diam. Integer sagittis odio sit amet sem vestibulum, ac vestibulum velit bibendum. Nam eros nisi, volutpat sit amet erat molestie, malesuada faucibus risus. Cras fermentum est ut neque gravida, eget scelerisque justo gravida. Nullam erat sem, varius quis pulvinar a, interdum nec ex. Nunc maximus enim et tellus consectetur, a finibus massa scelerisque. Donec neque lacus, maximus quis vehicula eget, congue in odio. Vivamus molestie orci quis purus accumsan vehicula a non metus. Donec pellentesque erat nec turpis pulvinar euismod. Nullam vel purus mi. Mauris ut nunc luctus, laoreet dui vitae, fringilla nisi. Pellentesque nec ornare justo. Donec quis diam sed nisl tincidunt vehicula quis sit amet mi. Nunc at orci ipsum. Cras aliquam sapien nunc, non faucibus sapien bibendum at. Sed dapibus felis id dolor condimentum fringilla. Fusce eget convallis arcu, vitae commodo velit. Donec vehicula malesuada eleifend. Ut maximus, neque id suscipit euismod, odio est lacinia tortor, vitae feugiat neque sem a mi. Mauris fringilla, erat vel cursus lobortis, nisi enim luctus dolor, in faucibus dolor justo eu tellus. Morbi egestas erat eu eros accumsan tincidunt. Donec arcu lorem.')));
        $length = random_int($minLength, $maxLength);

        $string = '';

        for($i = 0; $i < $length; $i++) {
            $string .= $lorem[random_int(0, count($lorem)-1)] . ' ';
        }

        return $string;
    }

    public function actionFixtures()
    {
        Review::deleteAll();
        Comment::deleteAll();
        ProposalContentHistory::deleteAll();
        Proposal::deleteAll();

        $now = (new \DateTime())->format('U');
        $sixMonthsAgo = (\DateTime::createFromFormat('Y-m-d H:i:s', '2019-06-01 00:00:00'))->format('U');

        $userIds = User::find()->select('id')->column();

        /* GENERATION PROPOSALS ET PROPOSAL CONTENT */

        $nbProposals = 100;

        for ($i = 0; $i < $nbProposals; ++$i) {
            $proposal = new Proposal();
            $proposal->title = self::getLorem(8, 20);
            $proposal->date = (\DateTime::createFromFormat('U', random_int($sixMonthsAgo, $now)))->format('Y-m-d H:i:s');
            $proposal->submitter_id = $userIds[random_int(0, count($userIds) - 1)];

            $is_published = random_int(0, 1);

            if($is_published) {
                $proposal->published = true;
                $proposal->social_media = 'twitter';
            } else {
                $proposal->published = false;
            }

            if(!$proposal->save()) {
                throw new Exception('Cannot save proposal');
            }

            $proposalContent = new ProposalContentHistory();
            $proposalContent->proposal_id = $proposal->id;
            $proposalContent->date = $proposal->date;
            $proposalContent->content = self::getLorem(100, 500);

            if(!$proposalContent->save()) {
                throw new Exception('cannot save proposal content');
            }
        }

        /* GENERATION COMMENTS */

        $proposals = Proposal::find()->all();

        foreach($proposals as $proposal) {
            $nbComments = random_int(0, 5);
            $minDate = \DateTime::createFromFormat('Y-m-d H:i:s', $proposal->date)->format('U');
            $maxDate = $now;

            /** @var User[] $usersWhoCanPostComment */
            $usersWhoCanPostComment = array_merge([$proposal->submitter], User::find()
                ->where(['role' => 'reviewer'])
                ->orWhere(['role' => 'publisher'])
                ->orWhere(['role' => 'admin'])
                ->all());

            for($i = 0; $i < $nbComments; ++$i) {
                $comment = new Comment();
                $comment->proposal_id = $proposal->id;
                $comment->author_id = $usersWhoCanPostComment[random_int(0, count($usersWhoCanPostComment)-1)]->id;
                $comment->content = self::getLorem(10, 50);
                $comment->date = (\DateTime::createFromFormat('U', random_int($minDate, $maxDate)))->format('Y-m-d H:i:s');

                if(!$comment->save()) {
                    throw new Exception('Cannot save comment');
                }
            }
        }

        /* GENERATION REVIEWS */

        foreach($proposals as $proposal) {
            $nbReviews = random_int(0, 5);
            $minDate = \DateTime::createFromFormat('Y-m-d H:i:s', $proposal->date)->format('U');
            $maxDate = $now;

            /** @var User[] $usersWhoCanReview */
            $usersWhoCanReview = User::find()
                ->where(['role' => 'reviewer'])
                ->orWhere(['role' => 'publisher'])
                ->orWhere(['role' => 'admin'])
                ->all();

            for($i = 0; $i < $nbReviews; ++$i) {
                $review = new Review();
                $review->proposal_id = $proposal->id;
                $review->reviewer_id = $usersWhoCanReview[random_int(0, count($usersWhoCanReview)-1)]->id;
                $review->date = (\DateTime::createFromFormat('U', random_int($minDate, $maxDate)))->format('Y-m-d H:i:s');
                $review->status = ['approved', 'disapproved'][random_int(0, 1)];

                if(!$review->save()) {
                    throw new Exception('cannot save review');
                }
            }
        }
    }

}