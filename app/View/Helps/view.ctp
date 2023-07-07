<?php
$this->assign('pagetitle', __('Help-page Detail'));
$this->Custom->addCrumb('Help-page Detail', array('controller' => 'helps', 'action' => 'index'));
$this->Custom->addCrumb('Help-page Detail');
$this->start('top_links');
// if(isset($helps)){
echo $this->Html->link(__('Edit'), array('controller' => 'helps', 'action' => 'edit', encrypt($help['Help']['id'])), array('icon' => 'fa-edit', 'title' => 'Click here to edit this help page', 'class' => 'btn btn-primary', 'escape' => false));
echo $this->Html->link(__('Back'), array('controller' => 'helps', 'action' => 'index'), array('icon' => 'fa-angle-double-left', 'class' => 'btn btn-default marginleft', 'escape' => false));
// }
$this->end();
?>


<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="panel panel-flat">
            <div class="panel-body">
                <table class="table table-bordered">
                    <tr>
                        <td>
                            Title
                        </td>
                        <td>
                            <?php echo $help['Help']['title']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Description
                        </td>
                        <td>
                            <?php echo $help['Help']['description']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Report Name
                        </td>
                        <td>
                            <?php echo $help['Help']['report_name']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="table-text">
                            Status:
                        </td>
                        <td>
                            <?php echo $help['Help']['status']; ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>