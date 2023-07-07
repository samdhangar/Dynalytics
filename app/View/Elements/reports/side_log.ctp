<div class="box box-primary">
    <div class="box-footer clearfix"> 
        <?php echo $this->element('paginationtopNew');?>
        <?php if (!empty($companyDetail)): ?>
            <!--            <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <strong>
            <?php echo __('Company Name:') ?>
                                </strong>
                                <span>
            <?php echo $companyDetail['Company']['first_name']; ?>
                                </span>
            
                            </div>
                        </div>-->
        <?php endif; ?>
    </div>
    <div class="box-body table-responsive no-padding">
        <?php
        $startNo = (int) $this->Paginator->counter('{:start}');

        ?>
        <table class="table table-hover table-bordered">
            <thead>
                <tr>
                    <?php $noOfFields = 20; ?>
                    
                    <th> 
                        <?php
                        echo $this->Paginator->sort('station', __('DynaCore Station ID'));

                        ?>
                    </th>
                    <?php if (!$this->Session->check('Auth.User.BranchDetail.id')) {?>
                    <th>
                        <?php
                        echo $this->Paginator->sort('Teller', __('User Type'));

                        ?>  
                    </th>
                    <?php }?>

                    <th> 
                        <?php
                        echo $this->Paginator->sort('name', __('User ID'));

                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('side_type', __('Side'));

                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('logon_datetime', __('Date Logged on'));

                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('logoff_datetime', __('Date Logged off'));

                        ?>
                    </th>
 <th> 
                        <?php
                        echo $this->Paginator->sort('logon_datetime', __('Time Logged on'));

                        ?>
                    </th>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('logoff_datetime', __('Time Logged off'));

                        ?>
                    </th>
                     <th> 
                        <?php
                        echo 'Total Time Logged In(Min)' ;

                        ?>
                    </th>

                </tr>
            </thead>
            <tbody>
                <?php if (empty($sideLogArr_new)): ?>
                    <tr>
                        <td colspan="<?php echo $noOfFields; ?>">
                            <?php echo __('No data available for selected period'); ?>
                        </td>
                    </tr>
                <?php endif; ?> 
                <?php foreach ($sideLogArr_new as $act): ?> 
                    <tr>

                        <td>
 
                            <?php echo isset($act[0]['station']) ? $act[0]['station'] : ''; ?>
                        </td>
                        <?php if (!$this->Session->check('Auth.User.BranchDetail.id')) {?>
                        <td class="table-text">
                            <?php echo isset($act[0]['Teller']) ? $act[0]['Teller'] : ''; ?>
                        </td>
                        <?php }?>
                        <td class="table-text">
                            <?php echo isset($act[0]['name']) ? $act[0]['name'] : ''; ?>
                        </td>
                        <td class="table-text">
                            <?php echo isset($act[0]['side_type']) ? $act[0]['side_type'] : ''; ?>
                        </td>
                        <td>
                            <?php echo isset($act[0]['logon_datetime']) ? date("m/d/Y", strtotime($act[0]['logon_datetime'])) : ''; ?>
                        </td>
                        <td>  
                            <?php echo isset($act[0]['logoff_datetime']) ? date("m/d/Y", strtotime($act[0]['logoff_datetime'])): ''; ?>
                        </td>
                        <td>
                            <?php echo isset($act[0]['logon_datetime']) ? date("h:i:s a", strtotime($act[0]['logon_datetime'])) : ''; ?>
                        </td>
                        <td>
                            <?php echo isset($act[0]['logoff_datetime']) ? date("h:i:s a", strtotime($act[0]['logoff_datetime'])) : ''; ?>
                        </td>
                        <td>
                            <?php
							//echo "login->".date("h:i:s", strtotime($act[0]['logon_datetime']))."<br />";
							//echo "logoff->".date("h:i:s", strtotime($act[0]['logoff_datetime']))."<br /><br />";
                              echo get_time_difference_new(date("h:i:s a", strtotime($act[0]['logon_datetime'])),date("h:i:s a", strtotime($act[0]['logoff_datetime'])));
                           /* $login_timeh=(int) ((strtotime($act[0]['logoff_datetime'])-strtotime($act[0]['logon_datetime']))/3600); 
                               $login_timem=(int) (((strtotime($act[0]['logoff_datetime'])-strtotime($act[0]['logon_datetime']))-$login_timeh*3600)/60);*/
?>
                            <?php //echo isset($act[0]['logoff_datetime']) ? $login_timeh.":".$login_timem  : ''; ?>
                        </td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="box-footer clearfix">
        <?php echo $this->element('paginationNew'); ?>
    </div>
</div>