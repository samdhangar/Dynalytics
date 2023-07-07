<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">

<?php
$siteUrl = Router::url('/', true);

?>
<html>
    <head>
        <title><?php echo $this->fetch('title'); ?></title>
    </head>
    <body>
        <div>
            <div style="overflow: hidden;">
                <div dir="ltr">
                    <div style="font-family:verdana,sans-serif;font-size:small;color:#000000"><br></div>
                    <div>
                        <div style="background-color:#ffffff">
                            <table align="center" width="600" style="margin:20px auto;padding:20px 0;border-collapse:collapse">
                                <tbody>
                                    <tr>
                                        <td style="margin:0;padding:0"></td>
                                        <td bgcolor="#FFFFFF" style="margin:0 auto;padding:0;border:1px solid #d5d5d5;display:block;max-width:600px;clear:both">
                                            <table bgcolor="#FFFFFF" style="margin:0;padding:0;width:100%;border-bottom:1px solid #d5d5d5">
                                                <tbody>
                                                    <tr>
                                                        <td style="margin:0;padding:0"></td>
                                                        <td style="margin:0;padding:10px 20px 10px 10px">
                                                            <table style="margin:0;padding:0;width:100%">
                                                                <tbody>
                                                                    <tr>
                                                                        <td width="170" style="margin:0;padding:0;color:#fff;font-size:12px">
                                                                            <?php
                                                                            echo $this->Html->link($this->Html->image($siteUrl . 'img/logo.png', array('width' => '170', 'alt' => Configure::read('Site.Name'), 'style' => 'max-width:100%')), array('controller' => 'users', 'action' => 'login'), array('escape' => false, 'title' => Configure::read('Site.Name'), 'style' => 'color:#fff', 'target' => '_blank'));

                                                                            ?>
                                                                        </td>
                                                                        <td align="right" style="margin:0;padding:0;font-family:Helvetica Neue,Helvetica,Helvetica,Arial,sans-serif;color:#777777;font-size:12px;line-height:14px;">
                                                                            <?php
                                                                            echo date('jS F Y');

                                                                            ?>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                        <td style="margin:0;padding:0"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table style="margin:0;padding:0;width:100%">
                                                <tbody>
                                                    <tr>
                                                        <td style="margin:0;padding:10px 15px 10px;color:#777;font-family:Helvetica Neue,Helvetica,Helvetica,Arial,sans-serif">
                                                            <?php
                                                            if (!empty($isFromView)):
                                                                echo '{BODY}';
                                                            else:
                                                                echo $this->fetch('content');
                                                            endif;
                                                            echo !empty($body) ? $body : '';
                                                            ?>                                           
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table cellspacing="0" style="margin:0;padding:0;border-top:1px solid #e5e5e5;color:#7b7b7b;width:100%">
                                                <tbody>
                                                    <tr>
                                                        <td bgcolor="#EEEEEE" style="background-image:url('<?php echo $siteUrl; ?>img/noise.png');margin:0;padding:10px 15px;font-size:10px">
                                                            <table style="margin:0;padding:0;width:100%">
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="margin:0;padding:0 10px 0 0;font-family:Helvetica Neue,Helvetica,Helvetica,Arial,sans-serif;font-size:12px;line-height:18px">
                                                                            <strong>
                                                                                <br>
                                                                                <?php
                                                                                echo $this->Html->link(Configure::read('Site.SupportEmail'), 'mailto:' . Configure::read('Site.SupportEmail'), array('escape' => false, 'style' => 'text-decoration:none; color: #777777;', 'target' => '_blank'));

                                                                                ?>
                                                                                <br>
                                                                                <?php
                                                                                echo $this->Html->link(Configure::read('Site.Name'), $siteUrl, array('escape' => false, 'style' => 'text-decoration:none; color: #777777;', 'target' => '_blank'));

                                                                                ?>
                                                                            </strong>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                        <td style="margin:0;padding:0"></td>
                                    </tr>
                                    <tr>
                                        <td style="margin:0;padding:0"></td>
                                        <td style="text-align:center;clear:both;max-width:600px;display:block;padding:0px;margin:0px auto">
                                            <img width="100%" src="<?php echo $siteUrl . 'img/email_shadow.png' ?>">
                                        </td>
                                        <td style="margin:0;padding:0"></td>
                                    </tr>
                                    <tr>
                                        <td style="margin:0;padding:0"></td>
                                        <td align="center" style="clear:both;max-width:600px;display:block;padding:0px;margin:0px auto;color:#aaaaaa;font-family:&quot;Helvetica Neue&quot;,&quot;Helvetica&quot;,Helvetica,Arial,sans-serif">
                                            <p style="margin-bottom:10px;font-weight:normal;font-size:12px">
                                                Questions? Please do not reply to this email; responses are not monitored. If you have questions, please use the contact information above.                                    
                                            </p>
                                        </td>
                                        <td style="margin:0;padding:0"></td>
                                    </tr>
                                    <tr>
                                        <td style="margin:0;padding:0"></td>
                                        <td align="center" style="clear:both;max-width:600px;display:block;padding:0px;margin:0px auto;color:#aaaaaa;font-family:Helvetica Neue,Helvetica,Helvetica,Arial,sans-serif">
                                            <p style="margin-bottom:10px;font-weight:normal;font-size:12px">
                                                Copyright © <?php echo date('Y'); ?> <?php echo Configure::read('Site.Name'); ?>. All Rights Reserved.
                                            </p>
                                        </td>
                                        <td style="margin:0;padding:0"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>


