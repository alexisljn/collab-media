<?php
/** @var \yii\data\ActiveDataProvider $usersDataProvider */

use yii\helpers\Html;

$this->title = 'Accounts';
?>

<div class="row">
    <div class="col-12">
        <h1>Accounts</h1>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <?php \yii\widgets\Pjax::begin();
        echo \yii\grid\GridView::widget([
            'dataProvider' => $usersDataProvider,
            'emptyText' => 'No registered users',
            'layout' => '{items}{pager}',
            'tableOptions' => ['class' => 'table box'],
            'headerRowOptions' => ['class' => 'box-header'],
            'rowOptions' => ['class' => 'box-row js-row-clickable'],
            'columns' => [
                'id',
                [
                    'attribute' => 'firstname',
                    'label' => 'Firstname',
                    'contentOptions' => ['class' => 'box-link'],
                    'format' => 'raw',
                    'value' => function($user) {
                        /** @var \app\models\databaseModels\User $user */

                        return '<a data-pjax="0" data-js-row-clickable-url href="accounts/' . $user->id . '">' . \yii\helpers\Html::encode($user->firstname) . '</a>';
                    }
                ],
                'lastname',
                'email',
                'role',
                [
                    'attribute' => 'is_validated',
                    'label'     => 'Validated Account',
                    'value'     => function ($user)
                    {
                        /** @var \app\models\databaseModels\User $user */

                        if($user->is_validated)
                        {
                            return "Yes";
                        }
                        else
                        {
                            return "No";
                        }

                    }
                ],
                [

                    'attribute' => 'is_active',
                    'label'     => 'Account Active',
                    'value'     => function ($user)
                    {
                        /** @var \app\models\databaseModels\User $user */

                        if($user->is_active)
                        {
                            return "Yes";
                        }
                        else
                        {
                            return "No";
                        }

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
        ]);
        \yii\widgets\Pjax::end();?>
    </div>
</div>

