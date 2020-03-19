<?php
/** @var string $error  */
use yii\helpers\Html;

$this->title = 'Create a proposal';
?>

<h1>Create a proposal</h1>

<?php
    if (!is_null($error)) {
        echo "TODO : Boostrap Alert";
    }
?>
<?php
    $form = yii\widgets\ActiveForm::begin([
        'id' => 'proposalForm',
        'options' => [
            'enctype' => 'multipart/form-data',
        ],
    ]);
?>
<?= $form->field($model, 'title')->textInput(['id' => 'proposalFormTitleInput']); ?>
<?= $form->field($model, 'content')->hiddenInput(['id' => 'proposalFormContentInput']); ?>
<div id="Proposalcontent" class="editSection"></div>
<div class="form-group mt-4 file-input-container">
    <label class="file-input-label">
        Pick or drop a file...
        <input type="file" id="manageproposalform-relatedfile" class="file-input" name="ManageProposalForm[relatedFile]">
    </label>
    <span class="file-input-filename"></span>
</div>
<?= yii\helpers\Html::submitButton('Submit', ['class' => 'btn btn-not-outline']); ?>
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


