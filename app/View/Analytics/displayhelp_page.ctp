<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title"><?php echo isset($helpPage['Help']['title']) ? $helpPage['Help']['title'] : "For this Report help page not Found"; ?></h4>
</div>
<div class="modal-body">
    <p><?php echo isset($helpPage['Help']['description']) ? $helpPage['Help']['description'] : "Coming soon"; ?></p>
</div>
<div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>