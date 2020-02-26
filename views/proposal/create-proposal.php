<?php
/** @var string $error  */
use yii\helpers\Html; ?>

<h1>Create Proposal</h1>

<?php
    if (!is_null($error)) {
        echo "TODO : Boostrap Alert";
    }
?>
<?php
    $form = yii\widgets\ActiveForm::begin([
        'id' => 'proposalForm',
    ]);
?>
<?= $form->field($model, 'title')->textInput(['id' => 'proposalFormTitleInput']); ?>
<?= $form->field($model, 'content')->hiddenInput(['id' => 'proposalFormContentInput']); ?>
<div id="Proposalcontent" class="editSection"></div>
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


