<?php
/** @var \yii\data\ActiveDataProvider $usersDataProvider */

use yii\helpers\Html; ?>
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
        [
            'attribute' => 'reset',
            'label'     => 'Reset',
            'format'    => 'raw',
            'value'     => function ($user)
            {
                /** @var \app\models\databaseModels\User $user */

                ob_start();
                ?>
                <form method="post" action="/management/reset-password/<?= $user->id; ?>">
                    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                    <input hidden name="redirect" value="/management/accounts">
                    <button type="submit">Reset Password</button>
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
    ]

]) ?>
