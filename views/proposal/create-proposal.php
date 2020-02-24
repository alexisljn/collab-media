<?php
echo 'CREATE PROPOSAL';
?>

MARKDOWN EDITOR
<div id="Proposalcontent" class="editSection"></div>

<button id="toto">Click</button>
<script type="text/javascript">
   let editor = new tui.Editor({
       el: document.querySelector('.editSection'),
       previewStyle: 'vertical',
       height: '300px',
       initialEditType: 'markdown'
   });
    let button = document.querySelector('#toto').addEventListener('click', ()=> {
        console.log(editor.getHtml());
    });
</script>


