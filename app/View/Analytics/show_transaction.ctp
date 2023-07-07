<?php
$this->assign('pagetitle', __('Display Transaction Line'));
$this->Custom->addCrumb(__('Display Transaction Line'));
$this->start('top_links');

echo $this->Html->link(__('Previous Transaction'), array('controller' => 'analytics', 'action' => 'previous_transaction', $file_id, $transaction_no), array('title' => __('Previous Transaction'), 'icon' => 'icon-database', 'class' => 'btn btn-primary btn-sm pull-right', 'escape' => false));
$this->end();
?>
<div class="box box-primary">
    <div id="editor"><?php echo !empty($fileData) ? $fileData : 'File Has No Content'; ?></div>
</div>
<?php 
echo $this->Html->script(
        array(
            'ace.min')); ?>
<script>
    var lineNumber = <?php echo $lineNo; ?>;
    var editor = ace.edit("editor");
    editor.resize(true);
    editor.setTheme("ace/theme/textmate");
    editor.scrollToLine(<?php echo $lineNo; ?>, true, true, function() {});
    editor.gotoLine(<?php echo $lineNo; ?>, 0, true);
    editor.renderer.scrollToLine(<?php echo $lineNo - 20; ?>);
    editor.session.setMode("ace/mode/text");
    editor.setReadOnly(true);
    editor.setOption("maxLines", 100);
    editor.session.addMarker(
    new ace.Range(<?php echo $lineNo - 1; ?>, 0, <?php echo $lineNo; ?>, 0),"ace_highlight-marker",
    );
</script>
<style type="text/css" media="screen">
    #editor {
        position: absolute;
        top: 83px;
        right: 0;
        bottom: 0;
        left: 0;
    }
.ace_highlight-marker {
   position: absolute; /* without this positions will be erong */
   background:rgba(255,0,0,0.3); /* color */ 
   z-index: 20; /* in front of all other markers */ 
    }
</style>