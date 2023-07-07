
<?php
    $this->assign('pagetitle', $message);
    $this->Custom->addCrumb(__('Error'));
?>
<div class="container-fluid text-center">
			<img src="/img/error/500.png" class="error_img img-responsive" alt="">
			<h1><?php echo __('Oops! Page not found.')?></h1>
			<h3 class="text-light m-b-20"><strong><?php echo __d('cake', 'Error'); ?>: </strong>
      <?php echo __d('cake', 'An Internal Error Has Occurred.'); ?></h3>

</div>

<?php
if (Configure::read('debug') > 0):
	echo $this->element('exception_stack_trace');
endif;
?>
