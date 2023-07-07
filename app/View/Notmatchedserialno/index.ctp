<?php
$this->assign('pagetitle', __('Not Matched Serial No'));
$this->Custom->addCrumb('Not Matched Serial No');
$startNo = (int) $this->Paginator->counter('{:start}');

?>

<div class="panel panel-flat">
    <div class="panel-heading">
        <h5 class="panel-title">List of all  <?php echo $this->fetch('pagetitle'); ?></h5>
    </div>
    <div class="dataTables_wrapper no-footer">
            <div class="datatable-header">
                <div class="dataTables_filter" style="width:100%;">   
                </div>
            </div>

    </div>

    <div class="dataTables_wrapper no-footer">
        <div class="datatable-header">
            <div style="float:none;" class="dataTables_filter">
                <?php echo $this->element('paginationtop'); ?>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover user-list" id="datatable">
            <thead>
                <tr>
                    <?php $columns = 4; ?>
                        <th width="5%">
                                <?php
                                    echo __('#');
                                ?>
                        </th>
                        <th width="25%"><?php echo $this->Paginator->sort('serial_no', __('Serial No')); ?></th>
                        <th width="25%"><?php echo $this->Paginator->sort('file_name', __('File Name')); ?></th>
                        <th width="15%"><?php echo $this->Paginator->sort('date', __('File Processed')); ?></th>
                </tr>
            </thead>
                            <tbody>

              <?php if (empty($get_serialData)) { ?>
                    <tr>
                        <td colspan='<?php echo $columns; ?>' class='text-warning'><?php echo __('No Data found.') ?></td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($get_serialData as $data): ?>
                        <tr>
                            <td>
                                <?php echo $startNo++; ?>
                            </td>
                            <td>
                                  <?php echo $data['Notmatchedserialno']['serial_no']; ?>
                            </td>
                            <td>
                                <?php echo $data['Notmatchedserialno']['file_name']; ?>
                            </td>
                            <td>
                                <?php echo $data['Notmatchedserialno']['date']; ?>
                            </td>
                            
                        </tr>
                    <?php endforeach; ?>
                <?php } ?>
            </tbody>
                        </table>
                    </div>
                      <div class="box-footer clearfix">



        <?php echo $this->element('pagination'); ?>
    </div>
    <?php echo $this->Form->end(); ?>
</div>

                </div>
