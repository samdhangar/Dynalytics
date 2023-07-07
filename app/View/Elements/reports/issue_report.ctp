<div class="box box-primary">
    <div class="box-footer clearfix">
        <?php
        echo $this->element('paginationtop');

        ?>
        <?php if (!empty($companyDetail) && !isDealer()): ?>
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
                    <?php $noOfFields = 9; ?>
                    <th>
                        <?php
                        echo __('Sr. No.');

                        ?>
                    </th>
                    <?php
//                    if (!empty($companyDetail) && !isDealer()):
                        $noOfFields++;

                        ?>
                        <th> 
                            <?php
                            echo $this->Paginator->sort('Company.first_name', __('Client Name'));

                            ?>
                        </th>
                    <?php // endif; ?>
					<?php if (!$this->Session->check('Auth.User.BranchDetail.id')) {?>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('Branch.name', __('Branch Name'));

                        ?>
                    </th>
					<?php }?>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('Ticket.ticket_date', __('Date'));

                        ?>
                    </th>

                    <th> 
                        <?php
                        echo __('DynaCore Station ID');

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('ErrorDetail.start_date', __('Open Date'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('Ticket.updated', __('Resolved Date'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('Dealer.first_name', __('Resolved By'));

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('Ticket.note', __('Last Note'));

                        ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tickets)): ?>
                    <tr>
                        <td colspan="<?php echo $noOfFields; ?>">
                            <?php echo __('No data available for selected period'); ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($tickets as $ticket): ?> 
                    <tr>
                        <td>
                            <?php echo $startNo++; ?>
                        </td>
                        <?php // if (!empty($companyDetail) && !isDealer()): ?>
                            <td>
                                <?php echo isset($ticket['Company']['first_name']) ? $ticket['Company']['first_name'] : ''; ?>
                            </td>
                        <?php // endif; ?>
						<?php if (!$this->Session->check('Auth.User.BranchDetail.id')) {?>
                        <td>
                            <?php
                            echo isset($ticket['Branch']['name']) ? $ticket['Branch']['name'] : '';

                            ?>
                        </td>
						<?php }?>
                        <td>
                            <?php
                            echo showdate($ticket['Ticket']['ticket_date']);

                            ?>
                        </td>

                        <td>
                            <?php
                            echo!empty($ticket['ErrorDetail']['FileProccessingDetail']['station']) ? $ticket['ErrorDetail']['FileProccessingDetail']['station'] : '';

                            ?>
                        </td>
                        <td>
                            <?php
                            echo isset($ticket['ErrorDetail']['start_date']) ? showdatetime($ticket['ErrorDetail']['start_date']) : '';

                            ?>
                        </td>
                        <td>
                            <?php
                            if ($ticket['Ticket']['status'] == 'Closed'):
                                echo showdatetime($ticket['Ticket']['updated']);
                            endif;

                            ?>
                        </td>
                        <td class="table-text">
                            <?php
                            if ($ticket['Ticket']['status'] == 'Closed' && isset($ticket['Dealer']['first_name'])):
                                echo $ticket['Dealer']['first_name'];
                            endif;

                            ?>
                        </td>
                        <td class="table-text">
                            <?php
                            echo isset($ticket['Ticket']['note']) ? $ticket['Ticket']['note'] : '';

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