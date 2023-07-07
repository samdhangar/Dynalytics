<table cellspacing="0" cellpadding="10px" style=" width:700px; height: auto; max-width:920px; margin:10px auto; padding: 10px;border:1px #CCC solid;">
    <tbody>
        <tr>
            <td>
                <table cellspacing="0" cellpadding="0" style="text-align:center; width:100%;border-bottom:1px solid #CCC;">
                    <tbody>
                        <tr style="">
                            <td style="  padding:5px 0; font-size:15px; font-weight: bold;border-bottom:1px solid #CCC;">
                                <table>
                                    <tr>
                                        <td>
                                            <?php
                                            $imageUrl = Router::url('/', true) . 'img/';
                                            echo $this->Html->link(
                                                $this->Html->image($imageUrl . 'logo.png', array('width' => '200px', 'alt' => Configure::read('Site.Name'))), array('controller' => 'users', 'action' => 'login'), array('escape' => false));

                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td  style="padding-top: 10px">
                                            <?php
                                            echo Configure::read('Site.Address1') . ',<br>' . Configure::read('Site.Address2') . ',<br>' .
                                            Configure::read('Site.State') . ', ' . Configure::read('Site.Country') . '<br>' .
                                            Configure::read('Site.SupportPhone');

                                            ?>
                                        </td>
                                    </tr>
                                </table> 
                            </td>
                            <td style="  padding:5px 0; font-size:20px; font-weight: bold;border-bottom:1px solid #CCC;">
                                <?php
                                $imageUrl = Router::url('/', true) . 'img/';
                                echo $this->Html->image($imageUrl . 'invoice.png', array('width' => '150px', 'alt' => __('INVOICE')));

                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 66%;">
                                <table>
                                    <tr>
                                        <td style="width: 100%;font-weight: bold">
                                            <?php echo __('Billed To:') ?> 
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="border-bottom: dotted 1px #000; width: 100%;">
                                            <?php echo $companyDetail['User']['first_name']; ?> 

                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="border-bottom: dotted 1px #000; width: 100%;">
                                            <?php echo $companyDetail['User']['phone_no']; ?> 
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="border-bottom: dotted 1px #000; width: 100%;">
                                            <?php echo $companyDetail['User']['email']; ?> 
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td style="width: 34%; text-align: right;">
                                <table  style=" text-align: right; width: 100%;">
                                    <tr>
                                        <td style="text-align: left;">
                                            <span style="width: 130px; display: inline-block;">
                                                <?php echo __('Invoice No.:') ?>
                                            </span>
                                            <span style="width: 80px; display: inline-block; text-align: left; padding-left: 5px;">
                                                <?php echo showInvoiceNo($invoiceDetail['Invoice']['id']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: left;">
                                            <span style="width: 130px; display: inline-block;">
                                                <?php echo __('Invoice Date:') ?>
                                            </span>
                                            <span style="width: 80px; display: inline-block; text-align: left; padding-left: 5px;">
                                                <?php echo showdate($invoiceDetail['Invoice']['invoice_date']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: left;">
                                            <span style="width: 130px; display: inline-block;">
                                                <?php echo __('Invoice Due Date:') ?>
                                            </span>
                                            <span style="width: 80px; display: inline-block; text-align: left; padding-left: 5px;">
                                                <?php echo showdate($invoiceDetail['Invoice']['billed_date']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <table style="width: 100%;border: 1px solid #ccc;margin-top: 10px;" cellspacing="0" cellpadding="0" >
                    <thead>
                        <tr style="font-weight: bold">
                            <th style="text-align: left; padding:7px 10px; font-size: 18px;border-right:1px solid #CCC;border-bottom:1px solid #CCC;">
                                <?php
                                echo __('Sr. No.');

                                ?>
                            </th>
                            <th style="text-align: left; padding:7px 10px; font-size: 18px;border-right:1px solid #CCC;border-bottom:1px solid #CCC;" class="text-center">
                                <?php
                                echo __('Particlars');

                                ?>                            </th>
                            <th style="text-align: left; padding:7px 10px; font-size: 18px;border-right:1px solid #CCC;border-bottom:1px solid #CCC;">
                                <?php
                                echo __('Price');

                                ?>  
                            </th>
                            <th style="text-align: left; padding:7px 10px; font-size: 18px;border-right:1px solid #CCC;border-bottom:1px solid #CCC;">
                                <?php
                                echo __('Quantity');

                                ?>                            </th>
                            <th style="text-align: left; padding:7px 10px; font-size: 18px;border-bottom:1px solid #CCC;">
                                <?php
                                echo __('Total');

                                ?>                            </th>
                        </tr>
                    </thead>
                    <?php
                    $grandTotal = $subTotal = $discountTotal = 0;

                    ?>
                    <tbody>
                        <?php
                        $srNo = 1;
                        foreach ($billingData as $bill):

                            ?>
                            <tr>
                                <td style="  padding:5px 10px; font-size: 18px;border-right:1px #CCC solid;">
                                    <?php echo $srNo++; ?>
                                </td>
                                <td  style="  padding:5px 10px; font-size: 18px;border-right:1px solid #CCC;">
                                    <?php echo $bill['particlars']; ?>
                                </td>
                                <td  style="  padding:5px 10px; font-size: 18px;border-right:1px solid #CCC;">
                                    <?php echo $bill['price']; ?>
                                </td>
                                <td  style="  padding:5px 10px; font-size: 18px;border-right:1px solid #CCC;">
                                    <?php echo $bill['quantity']; ?>
                                </td>
                                <td style="text-align: center;  padding:5px 10px 5px 15px; font-size: 18px; text-align: left;">
                                    <?php
                                    $subTotal = $subTotal + $bill['total'];
                                    echo $bill['total'];

                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" style="text-align:right; padding:5px 10px; font-size: 18px; border-right: 1px #CCC solid; border-top: 1px #CCC solid;">
                                <?php
                                echo __('Sub Total:');

                                ?>
                            </th>
                            <th style="padding:5px 10px 5px 15px; font-size: 18px;border-top:1px #CCC solid; text-align: left;">
                                <?php
                                echo $subTotal;

                                ?>
                            </th>
                        </tr>
                        <tr>
                            <th colspan="4" style="text-align:right; padding:5px 10px; font-size: 18px;  border-right: 1px #CCC solid; border-top:1px #CCC solid;">
                                <?php
                                echo __('Discount:') . '(' . $invoiceDetail['Invoice']['discount'] . '%)';

                                ?>
                            </th>
                            <th style="padding:5px 10px 5px 15px; font-size: 18px; border-top:1px #CCC solid; text-align: left;">
                                <span style="margin-left: -10px;">
                                    <span>-</span> 
                                    <?php
                                    $discountTotal = ($invoiceDetail['Invoice']['discount'] * $subTotal);
                                    echo $discountTotal;

                                    ?>
                                </span>
                            </th>
                        </tr>
                        <tr>
                            <th colspan="4" style="text-align:right; padding:5px 10px; font-size: 18px;border-right:1px solid #CCC; border-bottom:1px solid #CCC; border-top:1px solid #CCC;">
                                <?php
                                echo __('Total Amount Due:');

                                ?>
                            </th>
                            <th style="padding:5px 10px 5px 15px; font-size: 18px; border-bottom:1px solid #CCC; border-top:1px solid #CCC; text-align: left;">
                                <?php
                                $grandTotal = $subTotal - $discountTotal;
                                echo $grandTotal;

                                ?>
                            </th>
                        </tr>
                    </tfoot>
                </table>

            </td>
        </tr>
    </tbody>
</table>
<table cellspacing="0" cellpadding="0" style=" width:700px; height: auto; max-width:920px; margin:-10px auto;">
    <tr>
        <td style="vertical-align: bottom; text-align:center; font-size: 12px;">
            <?php
            echo __('Thank you for business with us');

            ?>
        </td>
    </tr>
</table>