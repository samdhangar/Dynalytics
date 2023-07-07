<?php
$this->assign('pagetitle', __('Billing'));
$this->Custom->addCrumb(__('Billing'));
$this->start('top_links');
//echo $this->Html->link(__('Add Invoice'), array('action' => 'add'), array('icon' => 'fa-plus', 'title' => __('Add Invoice'), 'class' => 'btn btn-primary', 'escape' => false));
$this->end();

?>


<div class="panel panel-flat">
    <div class="panel-heading">
        <h5 class="panel-title">List of all  <?php echo $this->fetch('pagetitle'); ?></h5>
    </div>

    <div class="dataTables_wrapper no-footer">
      <div class="datatable-header">
        <div class="dataTables_filter" style="width:100%;">
         <?php   echo $this->Form->create('Invoice', array('autocomplete' => 'off', 'novalidate' => 'novalidate'));


            echo $this->Form->input('id', array(
                'label' => __('Invoice No.'),
                'placeholder' => __('Invoice no.'),
                'required' => false,
                'type' => 'text',
                'class' => 'form-control ',
                'div' => array('class' => 'col-md-2 no-leftpadding')
            ));
//                    echo $this->Form->input('name', array(
//                        'label' => __('Name'),
//                        'placeholder' => __('name'),
//                        'required' => false,
//                        'class' => 'form-control',
//                        'div' => array('class' => 'col-md-3')
//                    ));
            if (isSuparAdmin()):
                echo $this->Form->input('customer_type', array(
                    'label' => __('Customer Type'),
                    'required' => false,
                    'empty' => __('Select Customer Type'),
                    'options' => array(
                        COMPANY => 'Financial Institution',
                        DEALER => DEALER
                    ),
                    'class' => 'form-control',
                    'div' => array(
                        'class' => 'col-md-2'
                )));
            endif;
            echo $this->Form->input('status', array(
                'label' => __('Status'),
                'required' => false,
                'empty' => __('Select Invoice'),
                'options' => array(
                    'paid' => __('Paid'),
                    'unpaid' => __('Unpaid')
                ),
                'class' => 'form-control',
                'div' => array(
                    'class' => 'col-md-2'
            )));

            echo $this->Form->input('billed_date', array(
                'label' => __('Month'),
                'required' => false,
                'type' => 'text',
                'empty' => __('Select Month'),
                'class' => 'form-control monthDate',
                'div' => array(
                    'class' => 'col-md-3'
            ))); ?>

            <label>&nbsp</label>
            <div class="col-md-<?php echo (isSuparAdmin())?'3':'5' ?> form-group no-padding">
                <?php
                echo $this->Form->submit(__('Search'), array('class' => 'btn btn-primary margin-right10', 'div' => false));
                echo $this->Html->link(__('Reset Search'), array('action' => 'index', 'all'), array('title' => __('reset search'), 'class' => 'btn btn-default marginleft'));

                ?>
            </div>

            <?php echo $this->Form->end(); ?>

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



        <?php
        $startNo = (int) $this->Paginator->counter('{:start}');

        ?>
        <table class="table table-hover user-list" id="datatable">
          <?php $noOfField = 9; ?>
          <thead>
              <tr>
                  <th><?php echo $this->Paginator->sort('id', __('Invoice No.')); ?></th>
                  <!--<th><?php echo $this->Paginator->sort('name'); ?></th>-->
                  <?php if (isSuparAdmin()): ?>
                      <?php $noOfField++; ?>
                      <th><?php echo $this->Paginator->sort('customer_type'); ?></th>
                  <?php endif; ?>
                  <th><?php echo $this->Paginator->sort('invoice_date',__('Invoice Month')); ?></th>
                  <th><?php echo $this->Paginator->sort('gross_amount'); ?></th>
                  <th><?php echo $this->Paginator->sort('discount'); ?></th>
                  <th><?php echo $this->Paginator->sort('billed_amount'); ?></th>
                  <th><?php echo $this->Paginator->sort('billed_date'); ?></th>
                  <th><?php echo $this->Paginator->sort('paid_date'); ?></th>
                  <th><?php echo $this->Paginator->sort('status'); ?></th>
                  <!--<th><?php echo $this->Paginator->sort('updated', __('Updated On')); ?></th>-->
                  <th class="actions text-center"><?php echo __('Actions'); ?></th>
              </tr>
          </thead>
            <tbody>
              <?php if (empty($invoices)) { ?>
                  <tr>
                      <td colspan='<?php echo $noOfField; ?>' class='text-warning'><?php echo __('No Invoice found.') ?></td>
                  </tr>
              <?php } else { ?>

                  <?php foreach ($invoices as $invoice): ?>
                      <tr>
                          <td><?php echo showInvoiceNo($invoice['Invoice']['id']); ?>&nbsp;</td>
  <!--                            <td>
                              <?php
                              $controller = strtolower(Inflector::pluralize($invoice['Invoice']['customer_type']));
                              echo $this->Html->link($invoice['Invoice']['name'], array('controller' => $controller, 'action' => 'view', encrypt($invoice['Invoice']['user_id'])));

                              ?>
                          </td>-->
                          <?php if (isSuparAdmin()): ?>
                              <td>
                                  <?php
                                  if ($invoice['Invoice']['customer_type'] == 'Company') {
                                     echo "Financial Institution";
                                   }else{
                                      echo $invoice['Invoice']['customer_type']; 
                                   } 
                                  ?>
                              </td>
                          <?php endif; ?>
                          <td>
                              <?php echo date('M-Y',  strtotime($invoice['Invoice']['invoice_date'])); ?>
                          </td>
                          <td>
                              <?php echo showAmount($invoice['Invoice']['gross_amount']); ?>
                          </td>
                          <td>
                              <?php echo showDiscount($invoice['Invoice']['discount']); ?>
                          </td>
                          <td>
                              <?php echo showAmount($invoice['Invoice']['billed_amount']); ?>
                          </td>
                          <td>
                              <?php echo showdate($invoice['Invoice']['billed_date']); ?>
                          </td>
                          <td>
                              <?php echo showdate($invoice['Invoice']['paid_date']); ?>
                          </td>
                          <td>
                              <?php
                              if (isSuparAdmin()):
                                  echo $this->Custom->getToggleButton(
                                      $invoice['Invoice']['status'], 'subStatusChange', array(
                                      'data-uid' => encrypt($invoice['Invoice']['id']),
                                      'data-id' => 'userStatus_' . $invoice['Invoice']['id']), array('paid', 'unpaid')
                                  );
                              else:
                                  echo $this->Custom->showStatus($invoice['Invoice']['status']);
                              endif;

                              ?>
                          </td>
  <!--                            <td>
                              <?php echo showdate($invoice['Invoice']['updated']); ?>
                          </td>-->
                          <td class="actions text-center">
                              <?php echo $this->Html->link('', array('action' => 'download', encrypt($invoice['Invoice']['id'])), array('target' => '__blank', 'icon' => 'icon-download fa-download', 'title' => __('Click here to download this invoice'))); ?>
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

</div>

</div>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.2.0/css/datepicker.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.2.0/js/bootstrap-datepicker.min.js"></script>

<script type="text/javascript">
    jQuery(document).ready(function () {
        validateSearch("InvoiceIndexForm", ["InvoiceId", "InvoiceName", "InvoiceCustomerType", "InvoiceStatus", "InvoiceBilledDate"]);

        jQuery('.subStatusChange').on('click', function () {
            var status = ($(this).hasClass('off')) ? 'paid' : 'unpaid';
            var $this = jQuery(this);
            if (confirm('<?php echo __('Are you sure ? want to change status as ') ?>' + status)) {
                loader('show');
                var uId = $(this).data('uid');
                jQuery.ajax({
                    url: BaseUrl + '<?php echo $this->params['controller']; ?>/change_status/' + uId + "/" + status,
                    type: 'post',
                    dataType: 'json',
                    success: function (response) {
                        loader('hide');
                        if (response.status == 'success') {
                            $this.toggleClass('off');
                            if (status == 'paid' && !$this.hasClass('btn-success')) {
                                $this.removeClass('btn-danger');
                                $this.addClass('btn-success');
                            } else {
                                $this.removeClass('btn-success');
                                $this.addClass('btn-danger');
                            }
                        }
                        alert(response.message);
                    },
                    error: function (e) {
                        loader('hide');
                    }
                });
            }
        });
    });
</script>
