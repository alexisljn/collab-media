<?php
echo 'CREATE PROPOSAL';

use yii\helpers\Html; ?>

<?php $form = yii\widgets\ActiveForm::begin([
//        'action' => '/proposal/validate-proposal',
        'id' => 'proposalForm',
        'enableClientValidation' => false
]);
echo $form->field($model, 'title')->textInput(['id' => 'proposalFormTitleInput']);
echo $form->field($model, 'content')->hiddenInput(['id' => 'proposalFormContentInput']); ?>
<div id="Proposalcontent" class="editSection"></div>

<button id="toto">Click</button>
<?= $form->field($model, 'relatedFile')->fileInput(); ?>
<?= yii\helpers\Html::submitButton('Submit'); ?>
<?php yii\widgets\ActiveForm::end(); ?>





<script type="text/javascript">
    const editor = new tui.Editor({
       el: document.querySelector('.editSection'),
       previewStyle: 'vertical',
       height: '300px',
       initialEditType: 'markdown'
    });

    const form = document.querySelector('#proposalForm');
     $(() => {
        $(form).on("submit", function() {
            $("#proposalFormContentInput").val(editor.getMarkdown());
        })
     })

</script>


