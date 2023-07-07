<div class="box box-primary">
    <div class="box-footer clearfix">
        <?php echo $this->element('paginationtop'); ?>
        <?php if (!empty($companyDetail)): ?>
            <!-- <div class="row">
                <div class="col-md-12 col-sm-12">
                    <strong>
                        <?php echo __('Company Name:') ?>
                    </strong>
                    <span>
                        <?php echo $companyDetail['Company']['first_name']; ?>
                    </span>

                </div>
            </div> -->
        <?php endif; ?>
    </div>
    <div class="box-body table-responsive no-padding">
        <?php
        $startNo = (int) $this->Paginator->counter('{:start}');

        ?>
        <table class="table table-hover table-bordered">
            <thead>
                <tr>
                    <?php $noOfFields = 7; ?>
                    <th class="text_align">
                        <?php
                        echo __('#');

                        ?>
                    </th>
                    <?php
                    if (empty($companyDetail)):
                        $noOfFields++;

                        ?>

<!--                        <th>
                            <?php
                            echo $this->Paginator->sort('Company.first_name', __('Company Name'));

                            ?>
                        </th>-->
                    <?php endif; ?>
                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('Branch.name', __('Branch Name'));

                        ?>
                    </th>
                    <th class="text_align">
                        <?php echo $this->Paginator->sort('FileProccessingDetail.station',__('DynaCore Station ID'));?>
                    </th>
                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('created_date', __('Log File Date'));

                        ?>
                    </th>
                    <th class="text_align" style="width: 100px">
                        <?php echo __('Number of times processed'); ?>
                    </th>
                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('processing_starttime', __('Initial Processing Date/Time'));

                        ?>
                    </th>
                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('processing_endtime', __('Latest Processing Date/Time'));

                        ?>
                    </th>
                    <th class="text_align">
                        <?php
                        echo $this->Paginator->sort('download_files', __('Loge File Download Link'));
                        ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                
                <?php if (empty($processFiles)): ?>
                    <tr>
                        <td colspan="<?php echo $noOfFields; ?>">
                            <?php echo __('No data available for selected period'); ?>
                        </td>
                    </tr>
                <?php endif; ?>
                
                <?php foreach ($processFiles as $processFile): ?>
                    <tr>
                        <td align="center" style="width: 3%;">
                            <?php echo $startNo++; ?>
                        </td>
                        <?php if (empty($companyDetail)): ?>
<!--                            <td>
                                <?php echo isset($processFile['Company']['first_name']) ? $processFile['Company']['first_name'] : ''; ?>
                            </td>-->
                        <?php endif; ?>
                        <td>
                            <?php echo isset($processFile['Branch']['name']) ? $processFile['Branch']['name'] : ''; ?>
                        </td>
                        <td>
                            <?php echo isset($processFile['FileProccessingDetail']['station']) ? $temp_station[$processFile['FileProccessingDetail']['station']] : ''; ?>
                        </td>
                        <td align="center">
                            <?php echo showdate($processFile['FileProccessingDetail']['file_date']); ?>
                        </td>
                        <td class="text_right">
                            <?php
                                // echo $this->Html->link($processFile['FileProccessingDetail']['processing_counter']);  
                                echo isset($processFile['FileProccessingDetail']['processing_counter']) ? $processFile['FileProccessingDetail']['processing_counter'] : '';
                            ?>
                        </td>
                        <td align="center">
                            <?php echo showdatetime($processFile['FileProccessingDetail']['processing_starttime'],'Y-m-d H:i:s'); ?>
                        </td>
                        <td align="center">
                            <?php echo showdatetime($processFile['FileProccessingDetail']['processing_endtime']); ?>
                        </td>
                        <td align="center">                        
                            <?php echo $this->Html->link("Download",array('controller'=>'analytics','action'=>'downloadProcessfile',base64_encode($processFile['FileProccessingDetail']['filename'])),array('class'=>'btn btn-default btn-sm'))?>
                            
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="box-footer clearfix">
        <?php echo $this->element('pagination'); ?>
    </div>
</div>
<a href="" class="download_link" target="_blank"></a>
<script type="text/javascript">

    function downloadFile(filename){
        var filename = filename;
        $.ajax({
            type: "POST",
            url: "<?php echo Router::url(array('controller'=>'analytics','action'=>'downloadProcessfile'));?>",
            dataType: 'json',
            encode  : true,
            data: {'filename':filename},
            success: function(data) {
                const obj = JSON.parse(JSON.stringify(data))
                //console.log(obj.download_link);
                $(".download_link").attr("href", obj.download_link);
                $(".download_link")[0].click(); 
            }
        });

    }

</script>
