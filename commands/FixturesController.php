<?php


namespace app\commands;


use app\commands\mainController\MainController;
use app\components\Util;
use app\models\databaseModels\Comment;
use app\models\databaseModels\File;
use app\models\databaseModels\Proposal;
use app\models\databaseModels\ProposalApprovementSetting;
use app\models\databaseModels\ProposalContentHistory;
use app\models\databaseModels\ProposalFileHistory;
use app\models\databaseModels\Review;
use app\models\databaseModels\ScheduledPublication;
use app\models\databaseModels\SocialMediaPermission;
use app\models\databaseModels\User;
use app\models\databaseModels\EnabledSocialMedia;
use app\models\exceptions\CannotSaveException;
use yii\helpers\Console;

class FixturesController extends MainController
{
    const USERS_COUNT = 12;
    const REVIEWERS_COUNT = 5;
    const PUBLISHERS_COUNT = 3;
    const ADMINS_COUNT = 1;

    const USERS_PASSWORD = 'password';

    const MIN_PROPOSALS_PER_USER = 2;
    const MAX_PROPOSALS_PER_USER = 11;

    const PROPOSALS_PERCENT_CHANCES_TO_HAVE_ASSOCIATED_FILE = 70;

    // 
    const REVIEWS_PERCENT_CHANGES_TO_BE_POSITIVE = 60;

    private $users = [];
    private $reviewers = [];
    private $publishers = [];
    private $admins = [];

    private $proposals = [];

    static function getLorem(int $minLength, int $maxLength) {
        $lorem = explode(' ', str_replace('.', '', str_replace(',', '', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce nec feugiat orci, ac vulputate augue. Proin mattis sed velit sodales congue. Donec eu leo nisi. Morbi pulvinar dolor quis ultrices iaculis. Phasellus ipsum mi, laoreet auctor urna sed, congue ultricies elit. Nunc vel ante nec nunc accumsan pretium. Donec tempus laoreet sapien, consequat ultricies ipsum facilisis nec. Mauris vel neque gravida, egestas dui vitae, rhoncus ipsum. Cras nec est pellentesque, viverra erat in, ornare augue. Interdum et malesuada fames ac ante ipsum primis in faucibus. Quisque vel libero non arcu laoreet vestibulum. In vel fermentum sem, vitae interdum arcu. Pellentesque mattis iaculis volutpat. Vestibulum suscipit consequat mauris vel pellentesque. Nullam lacinia lectus vitae posuere maximus. Mauris tortor odio, commodo tempus pulvinar in, condimentum in lorem. Integer congue purus a libero accumsan, et elementum odio maximus. Donec condimentum ac risus vitae pharetra. Fusce aliquam risus elit, laoreet viverra lacus commodo at. Donec eu blandit diam. Ut eget sapien egestas, dapibus elit at, scelerisque nunc. Suspendisse neque quam, ornare non tempor a, placerat quis magna. Quisque tincidunt quam id molestie sagittis. Fusce neque ligula, vestibulum eu risus quis, semper imperdiet nulla. Proin feugiat magna at nulla varius ornare. Nullam quis dui tempor, elementum dolor vel, posuere est. Etiam blandit odio at mi finibus, eget tristique nisi dignissim. Phasellus fringilla, nisi sit amet interdum molestie, diam ante venenatis nisi, et luctus nisi urna at lectus. Aenean ac dui non erat tempus interdum eu eget elit. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Pellentesque pulvinar risus lorem, nec luctus mi tempor quis. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Proin augue justo, porttitor in rutrum accumsan, fermentum vel magna. Ut a justo mattis, consequat felis et, porttitor felis. Vivamus at facilisis ex. Fusce in risus tincidunt, tempor nibh id, varius elit. Morbi lobortis ultrices ullamcorper. Donec a massa efficitur, auctor tellus at, maximus diam. Integer sagittis odio sit amet sem vestibulum, ac vestibulum velit bibendum. Nam eros nisi, volutpat sit amet erat molestie, malesuada faucibus risus. Cras fermentum est ut neque gravida, eget scelerisque justo gravida. Nullam erat sem, varius quis pulvinar a, interdum nec ex. Nunc maximus enim et tellus consectetur, a finibus massa scelerisque. Donec neque lacus, maximus quis vehicula eget, congue in odio. Vivamus molestie orci quis purus accumsan vehicula a non metus. Donec pellentesque erat nec turpis pulvinar euismod. Nullam vel purus mi. Mauris ut nunc luctus, laoreet dui vitae, fringilla nisi. Pellentesque nec ornare justo. Donec quis diam sed nisl tincidunt vehicula quis sit amet mi. Nunc at orci ipsum. Cras aliquam sapien nunc, non faucibus sapien bibendum at. Sed dapibus felis id dolor condimentum fringilla. Fusce eget convallis arcu, vitae commodo velit. Donec vehicula malesuada eleifend. Ut maximus, neque id suscipit euismod, odio est lacinia tortor, vitae feugiat neque sem a mi. Mauris fringilla, erat vel cursus lobortis, nisi enim luctus dolor, in faucibus dolor justo eu tellus. Morbi egestas erat eu eros accumsan tincidunt. Donec arcu lorem.')));
        $length = random_int($minLength, $maxLength);

        $string = '';

        for($i = 0; $i < $length; $i++) {
            $string .= $lorem[random_int(0, count($lorem)-1)] . ' ';
        }

        return $string;
    }

    /**
     * @throws \Throwable
     */
    public function actionGenerate()
    {
        $transaction = \Yii::$app->db->beginTransaction();

        try {
            $this->println('Emptying all tables', Console::BOLD);
            $this->emptyAllTables();

            $this->println('Emptying Proposal Related Files', Console::BOLD);
            $this->emptyProposalRelatedFilesDirectory();

            $this->println('Generating Enabled Social Media', Console::BOLD);
            $this->generateEnabledSocialMedia();

            $this->println('Generating Proposal Approvement Setting', Console::BOLD);
            $this->generateProposalApprovementSetting();

            $this->println('Generating users', Console::BOLD);
            $this->generateUsers();
            $this->print('Password for users: ', Console::BOLD);
            $this->println(self::USERS_PASSWORD, Console::BOLD, Console::FG_GREEN);

            $this->println('Generating ' . self::MIN_PROPOSALS_PER_USER . ' to ' . self::MAX_PROPOSALS_PER_USER . ' proposals per user', Console::BOLD);
            $this->generateProposals();

            $this->println('Commiting', Console::BOLD);
            $transaction->commit();
        } catch(\Throwable $e) {
            $this->println('Error occurred, rollback changes', Console::BOLD, Console::FG_RED);
            $this->emptyProposalRelatedFilesDirectory();
            $transaction->rollBack();
            throw $e;
        }

        $this->println('Fixtures successfully generated', Console::BOLD, Console::FG_GREEN);
    }

    private function emptyAllTables()
    {
        Comment::deleteAll();
        $this->println(Comment::tableName() . ' table emptied');

        EnabledSocialMedia::deleteAll();
        $this->println(EnabledSocialMedia::tableName() . ' table emptied');

        File::deleteAll();
        $this->println(File::tableName() . ' table emptied');

        ProposalApprovementSetting::deleteAll();
        $this->println(ProposalApprovementSetting::tableName() . ' table emptied');

        ProposalContentHistory::deleteAll();
        $this->println(ProposalContentHistory::tableName() . ' table emptied');

        ProposalFileHistory::deleteAll();
        $this->println(ProposalFileHistory::tableName() . ' table emptied');

        Review::deleteAll();
        $this->println(Review::tableName() . ' table emptied');

        Proposal::deleteAll();
        $this->println(Proposal::tableName() . ' table emptied');

        ScheduledPublication::deleteAll();
        $this->println(ScheduledPublication::tableName() . ' table emptied');

        SocialMediaPermission::deleteAll();
        $this->println(SocialMediaPermission::tableName() . ' table emptied');

        User::deleteAll();
        $this->println(User::tableName() . ' table emptied');
    }

    private function emptyProposalRelatedFilesDirectory()
    {
        $directoryPath = \Yii::getAlias('@app/uploaded-files/proposal-related-files/');
        foreach(scandir($directoryPath) as $file) {
            if(is_file($directoryPath . $file) and $file !== '.gitignore') {
                unlink($directoryPath . $file);
            }
        }
    }

    /**
     * @throws CannotSaveException
     */
    private function generateEnabledSocialMedia()
    {
        $facebook = new EnabledSocialMedia();
        $facebook->social_media_name = "facebook";
        $facebook->is_enabled = true;
        if(!$facebook->save()) {
            throw new CannotSaveException($facebook);
        }
        $twitter = new EnabledSocialMedia();
        $twitter->social_media_name = "twitter";
        $twitter->is_enabled = true;
        if(!$twitter->save()) {
            throw new CannotSaveException($twitter);
        }
        $linkedin = new EnabledSocialMedia();
        $linkedin->social_media_name = "linkedin";
        $linkedin->is_enabled = true;
        if(!$linkedin->save()) {
            throw new CannotSaveException($linkedin);
        }
    }

    /**
     * @throws CannotSaveException
     */
    private function generateProposalApprovementSetting()
    {
        $setting = new ProposalApprovementSetting();
        $setting->id = "main";
        $setting->required_review = 3;
        $setting->approvement_percent = 50;
        if(!$setting->save()) {
            throw new CannotSaveException($setting);
        }
    }

    private function generateProposals()
    {
        $now = new \DateTime();
        $sixMonthsAgo = new \DateTime('-6 month');

        $proposalAssociatedFiles = [];
        $proposalAssociatedFilesPath = \Yii::getAlias('@app/dev/fixtures/proposalSampleFiles/');
        foreach(scandir($proposalAssociatedFilesPath) as $fileOrDirectory) {
            $fileOrDirectoryPath = $proposalAssociatedFilesPath . $fileOrDirectory;
            if(is_file($fileOrDirectoryPath)) {
                $proposalAssociatedFiles[] = $fileOrDirectoryPath;
            }
        }

        foreach($this->users as $user) {
            $proposalsCount = random_int(self::MIN_PROPOSALS_PER_USER, self::MAX_PROPOSALS_PER_USER);

            $this->println("Generating $proposalsCount proposals for user {$user->id} ({$user->firstname} {$user->lastname})");

            for($i = 0; $i < $proposalsCount; ++$i) {
                $proposal = new Proposal();
                $proposal->title = self::getLorem(5, 15);
                $proposal->date = Util::getDateTimeFormattedForDatabase(
                    \DateTime::createFromFormat('U', random_int(
                        $sixMonthsAgo->format('U'),
                        $now->format('U')
                    )));
                $proposal->submitter_id = $user->id;

                $status = random_int(0, 2);
                switch ($status) {
                    case 0:
                        $proposal->status = \app\models\Proposal::STATUS_PENDING;
                        break;
                    case 1:
                        $proposal->status = \app\models\Proposal::STATUS_PUBLISHED;
                        $proposal->social_media = 'twitter';
                        break;
                    case 2:
                        $proposal->status = \app\models\Proposal::STATUS_REJECTED;
                        break;
                    default:
                        $proposal->status = \app\models\Proposal::STATUS_PENDING;
                        break;
                }

                if(!$proposal->save()) {
                    throw new CannotSaveException($proposal);
                }

                $proposalContent = new ProposalContentHistory();
                $proposalContent->proposal_id = $proposal->id;
                $proposalContent->date = $proposal->date;
                $proposalContent->content = self::getLorem(30, 200);

                if(!$proposalContent->save()) {
                    throw new CannotSaveException($proposalContent);
                }

                $hasFile = random_int(1, 100);
                if($hasFile <= self::PROPOSALS_PERCENT_CHANCES_TO_HAVE_ASSOCIATED_FILE) {
                    $fileToCopy = $proposalAssociatedFiles[random_int(0, count($proposalAssociatedFiles)-1)];
                    copy($fileToCopy, \Yii::getAlias("@app/uploaded-files/proposal-related-files/{$proposal->id}.jpg"));

                    $file = new File();
                    $file->proposal_id = $proposal->id;
                    $file->path = $proposal->id . '.jpg';

                    if(!$file->save()) {
                        throw new CannotSaveException($file);
                    }
                }

                $this->proposals[] = $proposal;
            }
        }
    }

    private function generateReviews()
    {

    }

    /**
     * @throws CannotSaveException
     * @throws \yii\base\Exception
     */
    private function generateUsers()
    {
        $this->println('Hashing password');
        $usersPasswordHash = \Yii::$app->security->generatePasswordHash(self::USERS_PASSWORD);

        $this->println('Generating ' . self::USERS_COUNT . ' users');
        for($i = 1; $i <= self::USERS_COUNT; ++$i) {
            $user = new User();
            $user->firstname = 'Sample';
            $user->lastname = 'User' . $i;
            $user->email = 'user' . $i . '@example.com';
            $user->password_hash = $usersPasswordHash;
            $user->role = \app\models\User::USER_ROLE_MEMBER;
            $user->is_validated = true;
            $user->is_active = true;

            if(!$user->save()) {
                throw new CannotSaveException($user);
            }

            $this->users[] = $user;

            $this->println("Generated user {$user->firstname} {$user->lastname} - {$user->email}");
        }

        $this->println('Generating ' . self::REVIEWERS_COUNT . ' reviewers');
        for($i = 1; $i <= self::REVIEWERS_COUNT; ++$i) {
            $user = new User();
            $user->firstname = 'Sample';
            $user->lastname = 'Reviewer' . $i;
            $user->email = 'reviewer' . $i . '@example.com';
            $user->password_hash = $usersPasswordHash;
            $user->role = \app\models\User::USER_ROLE_REVIEWER;
            $user->is_validated = true;
            $user->is_active = true;

            if(!$user->save()) {
                throw new CannotSaveException($user);
            }

            $this->reviewers[] = $user;
            $this->users[] = $user;

            $this->println("Generated reviewer {$user->firstname} {$user->lastname} - {$user->email}");
        }

        $this->println('Generating ' . self::PUBLISHERS_COUNT . ' publishers');
        for($i = 1; $i <= self::PUBLISHERS_COUNT; ++$i) {
            $user = new User();
            $user->firstname = 'Sample';
            $user->lastname = 'Publisher' . $i;
            $user->email = 'publisher' . $i . '@example.com';
            $user->password_hash = $usersPasswordHash;
            $user->role = \app\models\User::USER_ROLE_PUBLISHER;
            $user->is_validated = true;
            $user->is_active = true;

            if(!$user->save()) {
                throw new CannotSaveException($user);
            }

            $this->publishers[] = $user;
            $this->reviewers[] = $user;
            $this->users[] = $user;

            $this->println("Generated publisher {$user->firstname} {$user->lastname} - {$user->email}");
        }

        $this->println('Generating ' . self::ADMINS_COUNT . ' admins');
        for($i = 1; $i <= self::ADMINS_COUNT; ++$i) {
            $user = new User();
            $user->firstname = 'Sample';
            $user->lastname = 'Admin' . $i;
            $user->email = 'admin' . $i . '@example.com';
            $user->password_hash = $usersPasswordHash;
            $user->role = \app\models\User::USER_ROLE_ADMIN;
            $user->is_validated = true;
            $user->is_active = true;

            if(!$user->save()) {
                throw new CannotSaveException($user);
            }

            $this->admins[] = $user;
            $this->publishers[] = $user;
            $this->reviewers[] = $user;
            $this->users[] = $user;

            $this->println("Generated admin {$user->firstname} {$user->lastname} - {$user->email}");
        }
    }
}