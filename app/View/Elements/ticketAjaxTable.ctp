<ul class="nav nav-tabs">
    <li class="active">
        <?php
        $title = __('New');
        if (!empty($tickets['New'])) {
            $title .= '&nbsp;<i class="badge bg-green">' . $tickets['New'] . '</i>';
        }

        echo $this->Html->link($title, '#tab_1-1', array('data-toggle' => 'tab', 'escape' => false));

        ?>
    </li>
    <li class="">
        <?php
        $title = __('Open');
        if (!empty($tickets['Open'])) {
            $title .= '&nbsp;<i class="badge bg-green">' . $tickets['Open'] . '</i>';
        }

        echo $this->Html->link($title, '#tab_2-2', array('data-toggle' => 'tab', 'escape' => false));

        ?>
    </li>
    <li class="">
        <?php
        $title = __('Closed');
        if (!empty($tickets['Closed'])) {
            $title .= '&nbsp;<i class="badge bg-green">' . $tickets['Closed'] . '</i>';
        }
        echo $this->Html->link($title, '#tab_3-2', array('data-toggle' => 'tab', 'escape' => false));

        ?>
    </li>
    
</ul>
<div class="tab-content">
    <div id="tab_1-1" class="tab-pane active">
        <?php echo $this->element('ticketTable', array('dealers' => $dealers, 'ticketData' => $tickets['New'], 'ticketType' => 'new')); ?>
    </div>
    <div id="tab_2-2" class="tab-pane">
        <?php echo $this->element('ticketTable', array('ticketData' => $tickets['Open'], 'ticketType' => 'open')); ?>
    </div>
    <div id="tab_3-2" class="tab-pane">
        <?php echo $this->element('ticketTable', array('ticketData' => $tickets['Closed'], 'ticketType' => 'closed')); ?>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function () {
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
