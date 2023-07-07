<div class="box box-primary">
    <div class="box-footer clearfix">
        <?php
        echo $this->element('paginationtop');

        ?>
        <?php if (!empty($companyDetail)): ?>
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <strong>
                        <?php echo __('Company Name:') ?>
                    </strong>
                    <span>
                        <?php echo $companyDetail['Company']['first_name']; ?>
                    </span>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <div class="box-body table-responsive no-padding">
        <?php
        $startNo = (int) $this->Paginator->counter('{:start}');
        ?>
        <table class="table table-hover table-bordered">
            <thead>
                <tr>
                    <?php $noOfFields = 11; ?>
                    <th>
                        <?php
                        echo __('Sr. No.');

                        ?>
                    </th>
                    <?php
                    if (empty($companyDetail)):
                        $noOfFields++;

                        ?>
                        <th> 
                            <?php
                            echo $this->Paginator->sort('FileProccessingDetail.Company.first_name', __('Company Name'));

                            ?>
                        </th>
                    <?php endif; ?>
					<?php if (!$this->Session->check('Auth.User.BranchDetail.id')) {?>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('FileProccessingDetail.Branch.name', __('Branch Name'));

                        ?>
                    </th>
					<?php }?>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('FileProccessingDetail.Company.Dealer.name', __('Dealer Name'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('ErrorDetail.error_count', __('No. of Events'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('ErrorDetail.error_message', __('Error Message'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('ErrorDetail.entry_timestamp', __('Date error occurred'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('ErrorDetail.start_date', __('Date time reported'));

                        ?>
                    </th>
                    <th>
                        <?php
//                        echo $this->Paginator->sort('Ticket.ticket_date', __('Date time notification message'));
                        echo __('Date time notification message');

                        ?>
                    </th>
                    <th>
                        <?php
//                        echo $this->Paginator->sort('Ticket.acknowledge_date', __('Date time acknowledged'));
                        echo __('Date time acknowledged');

                        ?>
                    </th>
                    <th>
                        <?php
//                        echo $this->Paginator->sort('Ticket.updated', __('Date time resolved'));
                        echo __('Date time resolved');

                        ?>
                    </th>
                    <th>
                        <?php
//                        echo $this->Paginator->sort('Ticket.Dealer.first_name', __('Dealer Support Engineer'));
                        echo __('Dealer Support Engineer');

                        ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($errors)): ?>
                    <tr>
                        <td colspan="<?php echo $noOfFields; ?>">
                            <?php echo __('No data available for selected period'); ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($errors as $error): ?> 
                    <tr>
                        <td  align="center">
                            <?php echo $startNo++; ?>
                        </td>
                        <?php if (empty($companyDetail)): ?>
                            <td>
                                <?php echo isset($error['FileProccessingDetail']['Company']['first_name']) ? $error['FileProccessingDetail']['Company']['first_name'] : ''; ?>
                            </td>
                        <?php endif; ?>
						<?php if (!$this->Session->check('Auth.User.BranchDetail.id')) {?>
                        <td> 
                            <?php echo isset($error['FileProccessingDetail']['Branch']['name']) ? $error['FileProccessingDetail']['Branch']['name'] : ''; ?>
                        </td>
						<?php }?>
                        <td> 
                            <?php echo isset($error['FileProccessingDetail']['Company']['Dealer']['first_name']) ? $error['FileProccessingDetail']['Company']['Dealer']['first_name'] : ''; ?>
                        </td>
                        <td>
                            <?php echo $error['ErrorDetail']['error_count']; ?>
                        </td>
                        <td onclick="getErrorMessage(this)" class="errorMessage" data-action="analytics/getErrorDetail/" data-id="<?php echo encrypt($error['ErrorDetail']['id']) ?>">
                            <?php echo cropDetail($error['ErrorDetail']['error_message'], 50); ?>
                        </td>
                        <td align="center">
                            <?php echo showdatetime($error['ErrorDetail']['entry_timestamp']); ?>
                        </td>
                        <td align="center">
                            <?php echo showdatetime($error['ErrorDetail']['start_date']); ?>
                        </td>
                        <td align="center">
                            <?php
                            if(isset($error['Ticket'][0]['ticket_date'])):
                                echo showdatetime($error['Ticket'][0]['ticket_date']);
                            endif;

                            ?>
                        </td>
                        <td align="center">
                            <?php 
                            if(!empty($error['Ticket'][0]['is_acknowledge']) && isset($error['Ticket'][0]['acknowledge_date'])):
                                echo showdatetime($error['Ticket'][0]['acknowledge_date']);
                                
                            endif;
                            ?>
                        </td>
                        <td>
                            <?php 
                            if(isset($error['Ticket'][0]['status']) && ($error['Ticket'][0]['status'] == 'Closed')):
                                echo showdatetime($error['Ticket'][0]['updated']);
                                
                            endif;
                            ?>
                        </td>
                        <td>
                            <?php 
                            if(!empty($error['Ticket'][0]['dealer_id'])):
                                echo showdatetime($error['Ticket'][0]['Dealer']['first_name']);
                            endif;
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="box-footer clearfix">
        <?php echo $this->element('pagination'); ?>
    </div>
</div>