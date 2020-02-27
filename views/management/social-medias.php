<?php
/** @var \yii\data\ActiveDataProvider $socialMediasDataProvider */
/** @var \app\models\databaseModels\EnabledSocialMedia $socialMedia */

echo $socialMediasDataProvider->count;

use app\models\EnabledSocialMedia;
use yii\grid\GridView; ?>

<?= GridView::widget([
    'dataProvider' => $socialMediasDataProvider,
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
            'attribute' => 'is_enabled',
            'label'     => 'Social Media Active',
            'value'     => function ($socialMedia)
            {
                if($socialMedia->is_enabled)
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
            'value'     => function ($socialMedia)
            {
                return "<a href='/management/social-medias/" . $socialMedia->social_media_name . "'  id='modify-social-media'>Modify<a/>";
            }
        ],
    ],
]);
