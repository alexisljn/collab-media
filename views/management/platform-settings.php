<?php
/** @var \yii\data\ActiveDataProvider $socialMediasDataProvider */
/** @var \app\models\databaseModels\EnabledSocialMedia $socialMedia */
/** @var \app\models\forms\ProposalApprovementSettingForm $proposalApprovementSettingFormModel */

use app\models\EnabledSocialMedia;
use yii\grid\GridView;
use yii\helpers\Html;

$this->title = 'Settings';
?>

<div class="row">
    <div class="col-12">
        <h1>Platform settings</h1>
    </div>
    <div class="col-12">
        <h2 class="h5 mb-3">Proposal Approvement setting</>
    </div>
</div>
<?php $form = \yii\widgets\ActiveForm::begin([
    'id' => 'proposal-approvement-form',
    'action' => '/management/platform-settings',
    'method' => 'post'
]) ?>
<div class="row">
    <div class="col-lg-4 col-md-12">
        <?= $form->field($proposalApprovementSettingFormModel, 'required_review')->textInput() ?>
    </div>
    <div class="col-lg-4 col-md-12">
        <?= $form->field($proposalApprovementSettingFormModel, 'approvement_percent')->textInput() ?>
    </div>
</div>
<div class="row margin-bottom">
    <div class="col-12">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-not-outline']) ?>
    </div>
</div>
<?php yii\widgets\ActiveForm::end(); ?>
<div class="row">
    <div class="col-12">
        <h2 class="h5 mb-3">Social Media</>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <?= GridView::widget([
            'dataProvider' => $socialMediasDataProvider,
            'emptyText' => 'You have no social media configured',
            'layout' => '{items}{pager}',
            'tableOptions' => ['class' => 'table box'],
            'headerRowOptions' => ['class' => 'box-header'],
            'columns' => [
                [
                   'attribute' => 'social_media_name',
                    'label'     => 'Social Media Name',
                    'value' => function ($socialMedia)
                    {
                        return $socialMedia->social_media_name;
                    },
                    'contentOptions' => ['class' => 'social-media-name'],
                ],
                [
                    'label'     => 'Enabled',
                    'format'    => 'raw',
                    'value'     => function ($socialMedia)
                    {
                        /** @var  EnabledSocialMedia $socialMedia */
                        ob_start();
                        ?>
                        <form method="post" action="/management/enable-social-media/<?= $socialMedia->social_media_name; ?>">
                            <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                            <input type="checkbox"
                                   id="checkbox-<?= $socialMedia->social_media_name ?>"
                                   name="<?= $socialMedia->social_media_name ?>"
                                   <?php if ($socialMedia->is_enabled) { ?>
                                   checked>
                                    <?php } ?>
                        </form>
                        <?php
                        return ob_get_clean();
                    }
                ],
            ],
            'pager' => [
                'maxButtonCount' => 7,
                'firstPageLabel' => '<i class="fas fa-angle-double-left"></i>',
                'lastPageLabel' => '<i class="fas fa-angle-double-right"></i>',
                'prevPageLabel' => '<i class="fas fa-angle-left"></i>',
                'nextPageLabel' => '<i class="fas fa-angle-right"></i>'
            ],
        ]);?>
    </div>
</div>
<script type="text/javascript" id="proposal-enable-script">
    $('input[id^="checkbox"]').on('change', (e) => {
        console.log($('#checkbox-'+e.target.name).is(':checked'));
        $.post('/management/enable-social-media', {
            social_media_name: e.target.name,
            enabled: $('#checkbox-'+e.target.name).is(':checked')
        },
        (response) => {
            if(response == 200) {
                toastr.success('Success', 'Your change have been saved');
            }
        })
    })
</script>
