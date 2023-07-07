<div class="box box-primary">
    <style>
        .tooltip2 {
            position: relative;
            height: 100%;
            width: 100%;
            display: inline-block;
        }

        .tooltip2 .tooltiptext {
            visibility: hidden;
            width: 100px;
            height: 40px;
            background-color: black;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 5px 0;

            /* Position the tooltip */
            position: absolute;
            z-index: 1;
            bottom: 150%;
            left: 50%;
            margin-left: -60px;
        }

        .tooltip2 .tooltiptext::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: black transparent transparent transparent;
        }

        .tooltip2:hover .tooltiptext {
            visibility: visible;
        }
    </style>
    <div class="box-footer clearfix">
        <?php
        echo $this->element('paginationtop');
        ?>
                <?php if (!empty($filter_criteria)) : ?>
            <div class="row">
                <div class="col-md-6 text-left">
                    <strong>
                        <?php echo __('Filter Criteria') ?>
                    </strong>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-left">
                    <strong>
                        <?php echo __('Region:') ?>
                    </strong>
                    <span>
                        <?php echo $filter_criteria['region']; ?>
                    </span>
                    &nbsp;&nbsp;
                    <?php if (!empty($filter_criteria['branch'])) : ?>
                        <strong>
                            <?php echo __('Branch:') ?>
                        </strong>
                        <span>
                            <?php echo $filter_criteria['branch']; ?>
                        </span>
                    <?php endif; ?>
                    &nbsp;&nbsp;
                    <?php if (!empty($filter_criteria['station'])) : ?>
                        <strong>
                            <?php echo __('DynaCore Station ID:') ?>
                        </strong>
                        <span>
                            <?php echo $filter_criteria['station']; ?>
                        </span>
                    <?php endif; ?>
                    &nbsp;&nbsp;
                    <?php if (!empty($filter_criteria['selected_dates'])) : ?>
                        <strong>
                            <?php echo __('Selected Dates:') ?>
                        </strong>
                        <span>
                            <?php echo $filter_criteria['selected_dates']; ?>
                        </span>
                    <?php endif; ?>

                </div>
            </div>
        <?php endif; ?>
    </div>
    <div class="box-body table-responsive no-padding">
        <?php
        $startNo = (int) $this->Paginator->counter('{:start}');
        ?>
        <table class="table table-hover table-bordered">
            <thead>
                <tr>
                    <?php $noOfFields = 30; ?>
                    <th>
                        <?php
                        echo __('#');
                        ?>
                    </th>
                    <?php
                    if (!isCompany() && empty($companyDetail)) :
                        $noOfFields++;
                    ?>
                        <th class="text_align">
                            <?php
                            echo $this->Paginator->sort('Company.first_name', __('Company Name'));
                            ?>
                        </th>
                    <?php endif; ?>
                    <?php if (!$this->Session->check('Auth.User.BranchDetail.id')) { ?>
                        <th class="text_align">
                            <?php
                            echo $this->Paginator->sort('CompanyBranches.name', __('Branch Name'));
                            ?>
                        </th>
                    <?php } ?>
                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('FileProccessingDetail.station', __('DynaCore Station ID'));
                        ?>
                    </th>
                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('FileProccessingDetail.file_date', __('Date'));
                        ?>
                    </th>


                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('BillCount._1_actual_count', __('$1'));
                        ?>
                    </th>

                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('BillCount._2_actual_count', __('$2'));
                        ?>
                    </th>

                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('BillCount._5_actual_count', __('$5'));
                        ?>
                    </th>

                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('BillCount._10_actual_count', __('$10'));
                        ?>
                    </th>

                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('BillCount._20_actual_count', __('$20'));
                        ?>
                    </th>

                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('BillCount._50_actual_count', __('$50'));
                        ?>
                    </th>

                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('BillCount._100_actual_count', __('$100'));
                        ?>
                    </th>

                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('BillCount._1_actual_count', __('Actual Total'));
                        ?>
                    </th>

                </tr>
            </thead>
            <tbody>
                <?php if (empty($result_graph)) : ?>
                    <tr>
                        <td class="text_align" colspan="<?php echo $noOfFields; ?>">
                            <?php echo __('No data available for selected period'); ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($result_graph as $bill) : ?>
                    <tr>

                        <td class="text_align" style="width: 3%;">

                            <?php echo $startNo++; ?>
                        </td>
                        <?php if (!isCompany() && empty($companyDetail)) : ?>
                            <td class="table-text text_align">
                                <?php echo isset($bill['Company']['first_name']) ? $bill['Company']['first_name'] : ''; ?>
                            </td>
                        <?php endif; ?>
                        <?php if (!$this->Session->check('Auth.User.BranchDetail.id')) { ?>
                            <td class="table-text text_align">
                                <?php echo isset($bill['CompanyBranches']['name']) ? $bill['CompanyBranches']['name'] : ''; ?>
                            </td>
                        <?php } ?>
                        <td class="text_align">
                            <?php echo isset($bill['FileProccessingDetail']['station']) ? $temp_station[$bill['FileProccessingDetail']['station']] : ''; ?>
                        </td>
                        <td class="text_align">
                            <?php echo date('m/d/Y', strtotime($bill['FileProccessingDetail']['file_date'])); ?>
                        </td>


                        <td class="text_right" style="background-color: <?php echo $bill[0]['1_colour']; ?>;">
                            <div class="tooltip2">
                                <span class="tooltiptext">Lower Limit : <?php echo $bill['DenominationHeatMap']['1_lower']; ?> &#013; Upper Limit : <?php echo $bill['DenominationHeatMap']['1_upper']; ?></span>
                                <?php echo ($bill['BillCount']['_1_actual_count']); ?>
                            </div>

                        </td>

                        <td class="text_right" style="background-color: <?php echo $bill[0]['2_colour']; ?>;">
                            <div class="tooltip2">
                                <span class="tooltiptext">Lower Limit : <?php echo $bill['DenominationHeatMap']['2_lower']; ?> &#013; Upper Limit : <?php echo $bill['DenominationHeatMap']['2_upper']; ?></span>
                                <?php echo ($bill['BillCount']['_2_actual_count']); ?>
                            </div>

                        </td>

                        <td class="text_right" style="background-color: <?php echo $bill[0]['5_colour']; ?>;">
                            <div class="tooltip2">
                                <span class="tooltiptext">Lower Limit : <?php echo $bill['DenominationHeatMap']['5_lower']; ?> &#013; Upper Limit : <?php echo $bill['DenominationHeatMap']['5_upper']; ?></span>
                                <?php echo ($bill['BillCount']['_5_actual_count']); ?>
                            </div>

                        </td>

                        <td class="text_right" style="background-color: <?php echo $bill[0]['10_colour']; ?>;">
                            <div class="tooltip2">
                                <span class="tooltiptext">Lower Limit : <?php echo $bill['DenominationHeatMap']['10_lower']; ?> &#013; Upper Limit : <?php echo $bill['DenominationHeatMap']['10_upper']; ?></span>
                                <?php echo ($bill['BillCount']['_10_actual_count']); ?>
                            </div>

                        </td>

                        <td class="text_right" style="background-color: <?php echo $bill[0]['20_colour']; ?>;">
                            <div class="tooltip2">
                                <span class="tooltiptext">Lower Limit : <?php echo $bill['DenominationHeatMap']['20_lower']; ?> &#013; Upper Limit : <?php echo $bill['DenominationHeatMap']['20_upper']; ?></span>
                                <?php echo ($bill['BillCount']['_20_actual_count']); ?>
                            </div>

                        </td>

                        <td class="text_right" style="background-color: <?php echo $bill[0]['50_colour']; ?>;">
                            <div class="tooltip2">
                                <span class="tooltiptext">Lower Limit : <?php echo $bill['DenominationHeatMap']['50_lower']; ?> &#013; Upper Limit : <?php echo $bill['DenominationHeatMap']['50_upper']; ?></span>
                                <?php echo ($bill['BillCount']['_50_actual_count']); ?>
                            </div>

                        </td>

                        <td class="text_right" style="background-color: <?php echo $bill[0]['100_colour']; ?>;">
                            <div class="tooltip2">
                                <span class="tooltiptext">Lower Limit : <?php echo $bill['DenominationHeatMap']['100_lower']; ?> &#013; Upper Limit : <?php echo $bill['DenominationHeatMap']['100_upper']; ?></span>
                                <?php echo ($bill['BillCount']['_100_actual_count']); ?>
                            </div>

                        </td>

                        <?php

                        $total = (($bill['BillCount']['_1_actual_count'] * 1) + ($bill['BillCount']['_2_actual_count'] * 2) + ($bill['BillCount']['_5_actual_count'] * 5) + ($bill['BillCount']['_10_actual_count'] * 10) + ($bill['BillCount']['_20_actual_count'] * 20) + ($bill['BillCount']['_50_actual_count'] * 50) + ($bill['BillCount']['_100_actual_count'] * 100));
                        if ($bill['TransactionHeatMaps']['trans_lower'] > $total) {
                            $colour = '#F5BC47';
                        } else if ($total > $bill['TransactionHeatMaps']['trans_upper']) {
                            $colour = '#E43F4A';
                        } else {
                            $colour = '#BFBFBF';
                        }
                        ?>


                        <td class="text_right" style="background-color: <?php echo $colour; ?>;">
                            <div class="tooltip2">
                                <span class="tooltiptext">Lower Limit : <?php echo $bill['TransactionHeatMaps']['trans_lower']; ?> &#013; Upper Limit : <?php echo $bill['TransactionHeatMaps']['trans_upper']; ?></span>
                                <?php echo GetNumberFormat(((($bill['BillCount']['_1_actual_count'] * 1) + ($bill['BillCount']['_2_actual_count'] * 2) + ($bill['BillCount']['_5_actual_count'] * 5) + ($bill['BillCount']['_10_actual_count'] * 10) + ($bill['BillCount']['_20_actual_count'] * 20) + ($bill['BillCount']['_50_actual_count'] * 50) + ($bill['BillCount']['_100_actual_count'] * 100))), '$'); ?>
                            </div>

                        </td>


                        <!--                    <td>
                            <?php echo showdatetime($bill['BillCount']['created_date']); ?>
                        </td>-->
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>

            </tfoot>
        </table>
    </div>
    <div class="box-footer clearfix">
        <?php echo $this->element('pagination'); ?>
    </div>
</div>