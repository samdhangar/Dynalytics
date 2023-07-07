<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <?php echo $this->Html->meta('icon', 'img/favicon.png'); ?>
    <title>
        <?php echo strip_tags($this->fetch('pagetitle')) . ' - ' . Configure::read('Site.Name'); ?>
    </title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>


    <?php
    echo $this->Html->css(
        array(
            'assets/fonts/fonts',
            'assets/icons/icomoon/icomoon',
            'lib/daterangepicker/daterangepicker-bs3',
            'bootstrap/bootstrap-datepicker',
            'bootstrap/bootstrap-toggle.min',
            'animate.min',
            'bootstrap',
            'core',
            'layout',
            'bootstrap-extended',
            'components',
            'plugins',
            'loaders',
            'responsive',
            'color-system',
            'fancybox/jquery.fancybox',
            'custom'
        ),
        array('inline' => false)
    );

    echo $this->Html->script(
        array(
            'jquery',
            'bootstrap',
            'jquery.ui',
            'nav.accordion',
            'hammerjs',
            'jquery.hammer',
            'scrollup',
            'jquery.slimscroll',
            'smart-resize',
            'blockui.min',
            'wow.min',
            'fancybox.min',
            'venobox',
            'forms/uniform.min',
            'forms/switchery',
            'forms/select2.min',
            'forms/picker',
            'forms/picker.date',
            'forms/picker.time',
            'core',
            'user/morris.min',
            'lib/jquery.validate',
            'user/custom',
            'user/jquery-custom-validation',
            'lib/functions',
            'lib/address',
            'inputmask/inputmask',
            'inputmask/jquery.inputmask',
            'ckeditor/ckeditor',
            'lib/highcharts',
            'forms/spectrum',
            'pages/pickers',
            'lib/daterangepicker/daterangepicker'
        ),
        array('inline' => false)
    );
    echo $this->fetch('css');
    echo $this->fetch('script');
    ?>
    <script src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
         var BaseUrl = window.location.origin+'/';
        if(BaseUrl == 'http://localhost/'){
            var BaseUrl = 'http://localhost/dynalitics-master/';
        }
        var dateFrom = '<?php echo !empty($this->Session->read('Report.GlobalFilter')['from']) ? $this->Session->read('Report.GlobalFilter')['from'] : '';?>'
    jQuery(document).on("ready", function () {
        console.log($('.daterangepicker'));
        $('.daterangepicker').find('.ranges li').removeClass('active');
        var name = '';
        console.log($('.daterangepicker').find('.ranges li'));
        console.log(dateFrom);

        $('.daterangepicker').find('.ranges li').each(function(i){
            if(dateFrom == 'last_3months' && $(this).html() == 'Quarter to Date'){
                $(this).addClass('active');
            }else if(dateFrom == 'last_12months' && $(this).html() == 'Year to Date'){
                $(this).addClass('active');
            }else if(dateFrom == 'last_months' && $(this).html() == 'Last Month'){
                $(this).addClass('active');
            }else if(dateFrom == 'all_dates' && $(this).html() == 'All Dates'){
                $(this).addClass('active');
            }else if(dateFrom == 'customrange' && $(this).html() == 'Custom Range'){
                $(this).addClass('active');
            }
        })
    });

      
    </script>
</head>
<?php $theme = isset($arrConfigs['Site.Theme']['value']) ? $arrConfigs['Site.Theme']['value'] : Configure::read('Site.Theme') ?>

<body class="material-menu">
    <div id="preloader">
        <div id="status">
            <div class="loader">
                <div class="loader-inner ball-pulse">
                    <div class="bg-blue"></div>
                    <div class="bg-amber"></div>
                    <div class="bg-success"></div>
                </div>
            </div>
        </div>
    </div>
    <?php echo $this->element('user/header') ?>

    <?php $sesData = getMySessionData(); ?>
    <?php echo $this->element('user/' . strtolower($sesData['role']) . '_left') ?>


    <div class="absolute">


        <?php echo $this->Session->flash(); ?>

        <div id="alertMessage" role="alert" class="alert alert-primary no-border notification-message2" style="margin-top: 10px; float: left; width: 100%;display:none">
            <span id="defaultMsg" class="text-semibold">Message!</span> <span id="almessage"></span>
        </div>
    </div>
    <script type="text/javascript">
        function removealertmessage() {
            setTimeout(function() {
                jQuery('.notification-message2').slideUp('fast');
            }, 10000);
        }
    </script>

    </div>



    <section class="main-container">
        <!--Page Header-->
        <div class="header" style="margin-top:69px">
            <div class="header-content">
                <div class="page-title">
                    <i class="icon-select2 position-left"></i><?php echo $this->fetch('pagetitle'); ?>
                </div>
                <div class="elements">
                    <?php echo $this->fetch('top_links'); ?>
                </div>
                <div class="clearfix"></div>
                <?php echo $this->Custom->getCrumbs('', array('text' => '<i class="fa fa-home"></i> Home', 'url' => array('controller' => 'users', 'action' => 'dashboard'))); ?>

            </div>
        </div>
        <!--/Page Header-->


        <div class="container-fluid page-content">
            <?php echo $this->fetch('content'); ?>

        </div>

    </section>


    <div class="modal fade" id="smsModel" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
            </div>
        </div>
    </div>
    <div class="modal fade" id="SimpleModel" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
            </div>
        </div>
    </div>
    <div class="modal fade" id="HelpModel" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
            </div>
        </div>
    </div>
    <?php  echo $this->element('sql_dump'); ?>
</body>

</html>