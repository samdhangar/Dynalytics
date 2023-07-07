<div class="box box-primary">
    <div class="box-footer clearfix">
        <?php
        echo $this->element('paginationtop');

        ?>
    </div>
    <div class="box-body table-responsive no-padding">
        <?php
        $startNo = (int) $this->Paginator->counter('{:start}');

        ?>
        <table class="table table-hover table-bordered">
            <thead>
                <tr>
                    <?php $noOfFields = 5; ?>
                    <th>
                        <?php
                        echo __('Sr. No.');

                        ?>
                    </th>
                    <th>
                        <?php
                        echo $this->Paginator->sort('Company.first_name', __('Client Name'));

                        ?>
                    </th>
					<?php if (!$this->Session->check('Auth.User.BranchDetail.id')) {?>
                    <th> 
                        <?php
                        echo $this->Paginator->sort('Branch.name', __('Branch Name'));

                        ?>
                    </th>
					<?php }?>
                    <th>
                        <?php
                        echo __('Date period');

                        ?>
                    </th>
                    <?php
//                    if (!empty($companyDetail)):
                    $noOfFields = $noOfFields + 2;

                    ?>

                    <th> 
                        <?php
                        echo $this->Paginator->sort('FileProcessingDetail.station', __('DynaCore Station ID'));

                        ?>
                    </th>
                    <?php // endif; ?>
<!--                    <th> 
                    <?php
                    echo $this->Paginator->sort('Ticket.ticket_count', __('Total Error/Warnings'));

                    ?>
                </th>-->
                    <th>
                        <?php
                        echo $this->Paginator->sort('Ticket.ticket_date', __('Last Occurrence Date'));

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
                        <td class="table-text">
                            <?php echo isset($ticket['Company']['first_name']) ? $ticket['Company']['first_name'] : ''; ?>
                        </td>
						<?php if (!$this->Session->check('Auth.User.BranchDetail.id')) {?>
							<td class="table-text">
								<?php
								echo isset($ticket['Branch']['name']) ? $ticket['Branch']['name'] : '';

								?>
							</td>
						<?php }?>
                        <td>
                            <?php
                            echo showdate($this->Session->read('Report.ClientIssueReport.start_date')) . ' To ' . showdate($this->Session->read('Report.ClientIssueReport.end_date'));

                            ?>
                        </td>
                        <?php // if (!empty($companyDetail)): ?>

                        <td>
                            <?php
                            echo!empty($ticket['ErrorDetail']['FileProccessingDetail']['station']) ? $ticket['ErrorDetail']['FileProccessingDetail']['station'] : '';

                            ?>
                        </td>
                        <?php // endif; ?>
    <!--                        <td>
                        <?php
                        echo $ticket['Ticket']['ticket_count'];

                        ?>
                    </td>-->
                        <td>
                            <?php
                            echo showdatetime($ticket['Ticket']['ticket_date']);

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