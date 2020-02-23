<?php
echo 'CREATE PROPOSAL';
?>

MARKDOWN EDITOR
<div id="Proposalcontent" class="editSection"></div>
<script type="text/javascript">
   let editor = new tui.Editor({
       el: document.querySelector('.editSection'),
       previewStyle: 'vertical',
       height: '300px',
       initialEditType: 'markdown'
   })
</script>


