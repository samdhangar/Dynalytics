<?php
$this->assign('pagetitle', __('Help'));
$this->Custom->addCrumb('help');
$this->start('top_links');
echo $this->Html->link(__('Back'), $this->request->referer(), array('icon' => 'back', 'title' => __('Click here to go back'), 'class' => 'btn btn-default', 'escape' => false));
$this->end();

?>
<div class="row">
    <div class="col-xs-12">
         <div class="box box-primary">
             <div class="box-body">
                <div class="padding-tabs">
                    <div class="list-wrap">
                        <div class="panel-group-control panel-group" id="accordion1" role="tablist" aria-multiselectable="true">
                            <?php
                             if (empty($helps)) {
                                echo __("No Help page found");
                            } else {
                                $i = 0;
                                foreach ($helps as $key => $help){
                            ?>
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="headingOne">
                                    <h5 class="panel-title">
                                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne_<?php echo $help['Help']['id']?>" aria-expanded="true" aria-controls="collapseOne">
                                            <?php echo __($help['Help']['title']) ?>
                                        </a>
                                    </h5>

                                </div>
                                <?php
                                if($i == 0){
                                    $collapse = 'in';
                                }else{
                                    $collapse = '';
                                }
                                ?>
                                <div id="collapseOne_<?php echo $help['Help']['id']?>" class="panel-collapse collapse <?php echo $collapse;?>" role="tabpanel" aria-labelledby="headingOne">
                                    <div class="panel-body">
                                        <?php echo __($help['Help']['description']) ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                            $i++;
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
