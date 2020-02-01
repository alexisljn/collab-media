<?php
/** @var \yii\data\ActiveDataProvider $proposalsDataProvider */


?>

<?= \yii\grid\GridView::widget([
    'dataProvider' => $proposalsDataProvider,
    'columns' => [
        [
            'attribute' => 'title',
            'label' => 'Title',
            'format' => 'raw',
            'value' => function($proposal)
            {
//                dd($proposal);
                /** @var \app\models\databaseModels\Proposal $proposal */
                return '<a href="#">' . \yii\helpers\Html::encode($proposal->title) . '</a>';
                // return '<a data-pjax=0 href="/account/user/' . $user->id . '">' . Html::encode($user->first_name . ' ' . $user->last_name) . '</a>';
            }
        ],
        [
            'label' => 'Nombre Review',
            'value' => function($proposal)
            {
                /** @var \app\models\databaseModels\Proposal $proposal */

                return $proposal->getReviews()->count();
            }
        ],
        [
            'attribute' => 'id',
            'label' => 'id'
        ]
    ]
    ]) ?>