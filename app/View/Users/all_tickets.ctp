<style>
    body {
        font-family: Arial;
    }

    /* Style the tab */
    .tab {
        overflow: hidden;
        border: 1px solid #ccc;
        background-color: #f1f1f1;
    }

    /* Style the buttons inside the tab */
    .tab button {
        background-color: inherit;
        float: left;
        border: none;
        outline: none;
        cursor: pointer;
        padding: 14px 16px;
        transition: 0.3s;
        font-size: 17px;
    }

    /* Change background color of buttons on hover */
    .tab button:hover {
        background-color: #ddd;
    }

    /* Create an active/current tablink class */
    .tab button.active {
        background-color: #ccc;
    }

    /* Style the tab content */
    .tabcontent {
        display: none;
        padding: 6px 12px;
        border: 1px solid #ccc;
        border-top: none;
    }
</style>
<?php
$uri_get = (($this->request->here()));
$uri_get = explode('/', $uri_get);
if(in_array('new',$uri_get)){
    $ticketType = "new";
}
$this->assign('pagetitle', __('Tickets Data'));
$count_amount_flag = 0;
if (!empty($activityReportId)) {
    $this->Custom->addCrumb(__('Activity Reports'), array('controller' => $this->params['controller'], 'action' => 'activity_report'));
    $this->Custom->addCrumb(__('# %s', $activityReportId), array(
        'controller' => 'analytics',
        'action' => 'activity_report_view',
        encrypt($activityReportId)
    ), array('escape' => false, 'title' => __('Activity Report Id')));
    $this->Custom->addCrumb(__('Tickets Data'));
} else {
    $this->Custom->addCrumb(__('Tickets data'));
}
$this->start('top_links');

$this->end();
echo $this->Html->script('user/chart');
echo $this->Html->script('user/daterangepicker');
?>

<div class="panel panel-flat" style="float:left;width:100%;">
    <div class="tab">
        <?php echo $this->Form->postButton('New', array('controller' => 'users', 'action' => 'allTickets', 'new')); ?>
        <?php echo $this->Form->postButton('Open', array('controller' => 'users', 'action' => 'allTickets', 'open')); ?>
        <?php echo $this->Form->postButton('Closed', array('controller' => 'users', 'action' => 'allTickets', 'closed')); ?>
    </div>
    <div class="table-responsive htmlDataTable">
        <div class="box box-primary">
            <div class="box-footer clearfix">
                <?php
                echo $this->element('paginationtop'); ?>
            </div>

            <div class="box-body table-responsive no-padding">
                <?php
                    if ($ticketType == 'new'):
                        echo $this->Form->create('User', array('class' => 'assignAllForm', 'url' => array('controller' => 'tickets', 'action' => 'assigns'), 'id' => 'UserEditProfileForm'));
                    endif;
                $startNo = (int) $this->Paginator->counter('{:start}');
              $ticketColumns = 12;
                ?>
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <?php if (!isSupportDealer() && $ticketType == 'new'): ?>
                                <th>
                                    <?php
                                    if(!empty($tickets)){
                                        $disabled = '';
                                    }else{
                                        $disabled = "disabled";
                                    }
                                    // $disabled = !empty($tickets) ? '' : 'disabled';
                                    echo $this->Form->input('delete.all', array($disabled, 'hiddenField' => false, 'type' => 'checkbox', 'multiple' => 'checkbox', 'label' => '', 'class' => 'checkAll'));

                                    ?>
                                </th>
                            <?php endif; ?>
                            <th>
                                <?php echo __('Sr. No.') ?>
                            </th>
                            <th>
                                <?php echo __('Client Name') ?>
                            </th>
                            <?php if (!$this->Session->check('Auth.User.BranchDetail.id')) {?>
                            <th>
                                <?php echo __('Branch Name') ?>
                            </th>
                            <?php }?>
                            <th>
                                <?php echo __('Date') ?>
                            </th>
                            <th>
                                <?php echo __('DynaCore Station ID') ?>
                            </th>
                            <th>
                                <?php echo __('Errors/Warnings') ?>
                            </th>
                            <?php if ($ticketType != 'new'): ?>


                                <th>
                                    <?php echo __('Acknowledge Date') ?>
                                </th>
                                <th>
                                    <?php echo __('Assigned To') ?>
                                </th>
                                <th>
                                    <?php echo __('Note') ?>
                                </th>
                                <th>
                                    <?php echo __('Updated On') ?>
                                </th>
                                <?php if ($ticketType == 'open'): ?>

                                    <th>
                                        <?php echo __('Action') ?>
                                    </th>
                                <?php endif; ?>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($tickets)): ?>
                            <tr>
                                <td colspan="<?php echo $ticketColumns; ?>">
                                    <?php echo __('No %s tickets', $ticketType); ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($tickets as $ticket): ?>
                            <tr>
                                <?php if (!isSupportDealer() && $ticketType == 'new'): ?>
                                    <td>
                                        <?php
                                        echo $this->Form->input('delete.id.', array('value' => encrypt($ticket['Ticket']['id']), 'hiddenField' => false, 'type' => 'checkbox', 'multiple' => 'checkbox', 'label' => '', 'class' => 'deleteRow', 'required' => false));

                                        ?>
                                    </td>
                                <?php endif; ?>
                                <td>
                                    <?php echo $startNo++ ?>
                                </td>
                                <td class="table-text">
                                    <?php echo $ticket['Company']['first_name']; ?>
                                </td>
                                <?php if (!$this->Session->check('Auth.User.BranchDetail.id')) {?>
                                <td class="table-text">
                                    <?php echo $ticket['Branch']['name']; ?>
                                </td>
                                <?php }?>
                                <td align="center">
                                    <?php echo showdate($ticket['Ticket']['ticket_date']); ?>
                                </td>
                                <td>
                                    <?php echo $ticket['Ticket']['station']; ?>
                                </td>
                                <td class="ticketError" data-id="<?php echo encrypt($ticket['Ticket']['id']) ?>">
                                    <?php
                                    $ticket['Ticket']['error'] = $ticket['Ticket']['error'] . ' ' . $ticket['Ticket']['warning'];
                                    echo cropDetail($ticket['Ticket']['error'], 30);
                                    ?>
                                </td>
                                <?php if ($ticketType != 'new'): ?>
                                    <td align="center">
                                        <?php echo showdate($ticket['Ticket']['acknowledge_date']); ?>
                                    </td>
                                    <td class="table-text">
                                        <?php echo $ticket['Dealer']['name']; ?>
                                    </td>
                                    <td class="ticketError" data-id="<?php echo encrypt($ticket['Ticket']['id']); ?>">
                                        <?php echo cropDetail($ticket['Ticket']['note'], 30); ?>
                                    </td>
                                    <td>
                                        <?php echo showdate($ticket['Ticket']['updated']); ?>
                                    </td>
                                    <?php if ($ticketType == 'open'): ?>

                                        <td>
                                            <?php
                                            if ($ticket['Ticket']['status'] != 'Closed') {
                                                if(isSupportDealer() && empty($ticket['Ticket']['is_acknowledge'])){
                                                    echo $this->Html->link('', array('controller' => 'tickets', 'action' => 'add_ack', encrypt($ticket['Ticket']['id'])), array('icon'=>'fa-location-arrow','title'=>__('Click here to add acknowledge to ticket'),'data-message' => __('Are you sure want to ack to this ticket?'),'class' => 'ticketStatus'));
                                                }
                                                echo $this->Html->link('', array('controller' => 'tickets', 'action' => 'status_change', encrypt($ticket['Ticket']['id']), 'Closed'), array('icon'=>'fa-remove','title'=>__('Click here to close ticket'),'data-message' => __('Are you sure want to change the status to closed?'), 'class' => 'ticketStatus'));
                                            }

                                            ?>
                                        </td>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <?php if (!isSupportDealer() && $ticketType == 'new' && !empty($tickets)): ?>
                        <tfoot>
                            <tr>
                                <td colspan="<?php echo $ticketColumns ?>">
                                    <div class="deleteMultiple">
                                        <?php
                                        
                                        echo $this->Form->input('dealer_id', array('label' => false, 'class' => 'dealerId disabledBtn', 'disabled', 'empty' => __('Select Support'), 'div' => array('class' => 'no-margin paddingRight5')));
                                        echo $this->Form->submit(__('Assigned'), array('icon' => 'fa-plus', 'class' => 'btn btn-primary btn-xs disabled disabledBtn', 'id' => 'DeleteBtn1'));
                                        echo '</div>';

                                        ?>
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    <?php endif; ?>
                </table>
                
            </div>
            <div class="box-footer clearfix">
                <?php echo $this->element('pagination'); ?>
            </div>
        </div>
    </div>
</div>

<?php
if (isCompany()) {
    $sessionData = getMySessionData(); ?>
<?php
}

?>
   <?php
    if ($ticketType == 'new'):

        ?>

        <?php
        echo $this->Form->setValidation(array(
            'Rules' => array(
                'dealer_id' => array(
                    'required' => 1
                )),
            'Messages' => array(
                'dealer_id' => array(
                    'required' => __('Please select support')
                )
            )
            )
        );
        echo $this->Form->end();
    endif;

    ?>


<script type="text/javascript">
    // 'use strict'; 
    jQuery(document).ready(function() {

        var url_get = window.location.pathname;

        var result = url_get.split('/');
        var i = 0;
        $(".tab").find('button').each(function() {
            if(result.includes("new") && i == 0) {
                $(this).addClass('active');
            }else if (result.includes("open") && i == 1) {
                $(this).addClass('active');
            } else if(result.includes("closed") && i == 2) {
                $(this).addClass('active');
            }
            i++;
        });
        
        var prev = $("li.active").prev().find("a").attr("href");
            $("li.prev").find("a").attr("href", prev);

            var next = $("li.active").next().find("a").attr("href");
            $("li.next").find("a").attr("href", next);
            
            var disable = $("li.active").next().hasClass("next");
            if (disable) {
                $("li.next").find("a").attr("href", null);
            }

       

        jQuery('.ticketStatus').on('click', function (e) {
            e.preventDefault();
            var message = jQuery(this).data('message');
            var ticketUrl = jQuery(this).attr('href');
            if (confirm(message)) {
                loader('show');
                jQuery.ajax({
                    url: ticketUrl,
                    type: 'get',
                    success: function (response)
                    {
                        loader('hide');
                        jQuery("#smsModel .modal-content").html(response);
                        jQuery("#smsModel").modal('show');
                    },
                    error: function () {
                        jQuery("#smsModel").modal('hide');
                        loader('hide');
                    }
                });
            }
        });
        $(".checkAll").click(function () {
            $('.deleteRow').prop('checked', this.checked);
            if ($('#DeleteBtn').hasClass('disabled')) {
                $('#DeleteBtn').removeClass('disabled');
            }
            if (jQuery('.disabledBtn').hasClass('disabled')) {
                jQuery('.disabledBtn').removeClass('disabled');
            }
            jQuery('.disabledBtn').removeAttr('disabled');
            if ($("input:checkbox:checked").length == 0) {
                $('#DeleteBtn').addClass('disabled');
                jQuery('.disabledBtn').addClass('disabled');
                jQuery('.disabledBtn').attr('disabled', 'disabled');
            }
        });

        $('.deleteRow').click(function () {

            if (($("input:checkbox").length - 1) == $('input:checkbox:checked').length)
            {
                $('.checkAll').prop('checked', this.checked);
            }
            if ($('#DeleteBtn').hasClass('disabled')) {
                $('#DeleteBtn').removeClass('disabled');
            }

            if (jQuery('.disabledBtn').hasClass('disabled')) {
                jQuery('.disabledBtn').removeClass('disabled');
            }
            jQuery('.disabledBtn').removeAttr('disabled');

            if ($('input:checkbox:checked').length == 0) {
                $('#DeleteBtn').addClass('disabled');
                jQuery('.disabledBtn').addClass('disabled');
                jQuery('.disabledBtn').attr('disabled', 'disabled');
            }
        });

        jQuery('.deleteAllForm').on('submit', function (e) {
            if (!confirm(jQuery(this).data('confirm'))) {
                e.preventDefault();
            }
        });
    });

   
</script>