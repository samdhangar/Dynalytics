<div role="alert" class="alert alert-primary no-border notification-message" style="margin-top:10px;float: left;
    width: 100%;">
    <span class="text-semibold">Message!</span>

    <?php echo $message;?>
</div>

<script type="text/javascript">
    jQuery(document).ready(function(){
        setTimeout(function() {
            jQuery('.notification-message').slideUp('fast');
        }, 10000);
    });
</script>
