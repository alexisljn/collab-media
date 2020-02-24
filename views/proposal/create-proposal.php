<?php
echo 'CREATE PROPOSAL';

use yii\helpers\Html; ?>

<?php $form = yii\widgets\ActiveForm::begin([
        'action' => '#'
]);
echo $form->field($model, 'title');
//echo $form->field($model, 'content')->hiddenInput(); ?>
Content
<div id="Proposalcontent" class="editSection"></div>

<button id="toto">Click</button>
<?= $form->field($model, 'relatedFile')->fileInput(); ?>
<?= yii\helpers\Html::submitButton('Submit'); ?>
<?php yii\widgets\ActiveForm::end(); ?>

<script type="text/javascript">
   let editor = new tui.Editor({
       el: document.querySelector('.editSection'),
       previewStyle: 'vertical',
       height: '300px',
       initialEditType: 'markdown'
   });
    let button = document.querySelector('#toto').addEventListener('click', ()=> {
        console.log(editor.getMarkdown());
    });
</script>


