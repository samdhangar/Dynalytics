<!DOCTYPE html>
<html>
    <head>
        <?php echo $this->Html->charset(); ?>
        <?php echo $this->Html->meta('icon', 'img/favicon.png'); ?>
        <title><?php echo!empty($title_for_layout) ? $title_for_layout : Configure::read('Site.Name') ?></title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <?php
            echo $this->Html->css(
                array(
                'assets/fonts/fonts',
                'assets/icons/icomoon/icomoon',
                'bootstrap',
                'core',
                'bootstrap-extended',
                'plugins',
                'color-system',
                ), array('inline' => false)
            );
            echo $this->Html->script(
                array(
                'jquery',
                'bootstrap.js',
                'forms/uniform.min',
                'lib/jquery.validate',
                ), array('inline' => false)
            );
            echo $this->fetch('css');
            echo $this->fetch('script');
        ?>
    </head>
	<?php $theme = isset($arrConfigs['Site.Theme']['value'])?$arrConfigs['Site.Theme']['value']:Configure::read('Site.Theme')?>

	<body  style="height:100%; background:url('css/assets/images/assets/login_bg.jpg') no-repeat 0 0; background-size:cover;" class="skin-fullblack loginBody <?php echo $theme;?>">
        <div class="form-box container-fluid page-content">


        </div>
    <center class="margin-bottom20">
        <?php // echo $this->Html->link($this->Html->image('logo.png'), array('controller' => 'applicants', 'action' => 'index'), array('escape' => false, 'class' => 'logo')) ?>
        <?php //echo $this->Html->link($this->Html->image('logo.png'), '/', array('escape' => false, 'class' => 'logo')) ?>
    </center>

    <div class="container-fluid page-content">

				<div class="row panel-body">
          <div class="col-md-4 col-sm-4"></div>
					<div class="col-md-4 col-sm-4">
      <?php echo $this->Session->flash(); ?>
    </div>
    <div class="col-md-4 col-sm-4"></div>
  </div>
</div>
    <?php echo $this->fetch('content'); ?>
</body>
</html>
