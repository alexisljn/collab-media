<?php
/** @var \yii\data\ActiveDataProvider $usersDataProvider */

echo $usersDataProvider->count;
//dd($usersDataProvider);
?>
<?= \yii\grid\GridView::widget([
    'dataProvider' => $usersDataProvider,
    'columns' => [
        'id',
        'firstname',
        'lastname',
        'email',
        'role',
        [
           'attribute' => 'is_validated',
           'label'     => 'Validated Acount',
           'value'     => function ($user)
            {
                /** @var \app\models\databaseModels\User $user */

                if($user->is_validated)
                {
                    return "YES";
                }
                else
                {
                    return "NO";
                }

            }
        ],
        [

            'attribute' => 'is_active',
            'label'     => 'Acount Active',
            'value'     => function ($user)
            {
                /** @var \app\models\databaseModels\User $user */

                if($user->is_active)
                {
                    return "YES";
                }
                else
                {
                    return "NO";
                }

            }
        ],
        [
            'attribute' => 'modify',
            'label'     => 'Modify',
            'format'    => 'raw',
            'value'     => function ($user)
            {
                /** @var \app\models\databaseModels\User $user */

                return "<a href='/management/accounts/" . $user->id . "'  id='modify-account'>Modify Account<a/>";
            }
        ],


    ],

]) ?>
