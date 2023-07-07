<?php
$this->assign('pagetitle', __('List of all Denomination Heat Maps Configuration'));
$this->Custom->addCrumb(__('List of all Denomination Heat Maps'));
$startNo = (int) $this->Paginator->counter('{:start}');
$this->start('top_links');
echo $this->Html->link(__('List of all Denomination Heat Maps'), array('action' => 'add'), array('icon' => 'cc icon-plus3 add', 'title' => __('List of all Denomination Heat Maps'), 'class' => 'btn btn-sm btn-success ', 'escape' => false));
$this->end(); 
//generate search panel
 
?> 
                <div class="panel panel-flat">
                    <div class="panel-heading">
                        <h5 class="panel-title"><?php echo $this->fetch('pagetitle'); ?></h5>
                    </div>
                    
                    <div class="dataTables_wrapper no-footer">
                      <div class="datatable-header">
                        <div class="dataTables_filter" style="width:100%;">
                            <div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-body row">
                <div class="col-md-12">
                    <?php
                    echo $this->Form->create('DenominationHeatMap', array('autocomplete' => 'off', 'novalidate' => 'novalidate'));
                    echo $this->Form->input('name', array('label' => __('Denomination Heat Map Name'), 'placeholder' => __('Denomination Heat Map Name'), 'required' => false, 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));
                       echo $this->Form->input('branch_id', array('id' => 'analyBranchId', 'label' => __('Branch Name '), 'empty' => __('Select All'), 'class' => 'form-control', 'div' => array('class' => 'col-md-3')));
                     
                    ?>

                    <label>&nbsp</label>
                    <div class="col-md-3 form-group">
                        <?php
                         echo "<label for='analyBranchId' >&nbsp;</label><br>";
                        echo $this->Form->submit(__('Search'), array('class' => 'btn btn-primary margin-right10', 'div' => false));
                          echo $this->Html->link(__('Reset Search'), array('action' => 'index', 'all'), array('title' => __('reset search'), 'class' => 'btn btn-default'));
                        ?>
                    </div>

                    <?php echo $this->Form->end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
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
                        <table class="table table-hover user-list" id="datatable">
                            <thead>

                                 <tr>

                    <?php $columns = 7; ?>
                    <th rowspan="2" width="5%">
                            <?php
                                echo __('#');
                            ?>
                        </th>
                        <th rowspan="2" width="<?php echo (isAdmin()) ? '20%' : '20%' ?>"><?php echo $this->Paginator->sort('name', __('Denomination Heat Map Name')); ?></th>
                       
                        <th rowspan="2" width="10%"><?php echo $this->Paginator->sort('branch_name', __('Branch Name')); ?></th>
                          <th rowspan="2" width="10%"><?php echo $this->Paginator->sort('branch_name', __('DynaCore Station ID')); ?></th>
                        <th width="6%"   colspan="2"  style="border: 1px solid ; border-color: #ddd; !important"><center><?php echo  ('$1'); ?></center></th> 
                         <th width="6%"  style="border: 1px solid ;  border-color: #ddd; !important" colspan="2"><center><?php echo ('$2 '); ?></center></th> 
                          <th width="6%"  style="border: 1px solid ;  border-color: #ddd; !important"  colspan="2"><center><?php echo ('$5'); ?></center></th> 
                           <th width="6%"  style="border: 1px solid ;  border-color: #ddd; !important" colspan="2"><center><?php echo ('$10'); ?></center></th> 
                            <th width="6%"  style="border: 1px solid ; border-color: #ddd; !important" colspan="2"><center><?php echo ('$20 '); ?></center></th> 
                             <th width="6%"  style="border: 1px solid ;  border-color: #ddd; !important" colspan="2"><center><?php echo ('$50 '); ?></center></th>
                              <th width="6%"  style="border: 1px solid ;  border-color: #ddd; !important" colspan="2"><center><?php echo('$100 '); ?></center></th>  
                        <th rowspan="2" width="8%" class="actions text-center"><?php echo __('Actions'); ?></th>
                </tr>
                 <tr>

                    <?php $columns = 7; ?>
                    
                       
                        
                         <th width="3%" style="border: 1px solid;  border-color: #ddd; !important">
                         <center>  Lower</center> 

                            </th>
                             <th width="3%" style="border: 1px solid ; border-color: #ddd; margin-left: 3px; !important">
                           <center>   Upper </center> 

                            </th>
                          <th width="3%" style="border: 1px solid; border-color: #ddd; !important">
                         <center>  Lower</center> 

                            </th>
                             <th width="3%" style="border: 1px solid ;  border-color: #ddd; !important">
                           <center>   Upper </center> 

                            </th> 
                          <th width="3%" style="border: 1px solid;  border-color: #ddd; !important">
                         <center>  Lower</center> 

                            </th>
                             <th width="3%" style="border: 1px solid ;  border-color: #ddd; !important">
                           <center>   Upper </center> 

                            </th>
                            <th width="3%" style="border: 1px solid;  border-color: #ddd; !important">
                         <center>  Lower</center> 

                            </th>
                             <th width="3%" style="border: 1px solid ;  border-color: #ddd; !important">
                           <center>   Upper </center> 

                            </th>
                             <th width="3%" style="border: 1px solid; border-color: #ddd; !important">
                         <center>  Lower</center> 

                            </th>
                             <th width="3%" style="border: 1px solid ;  border-color: #ddd; !important">
                           <center>   Upper </center> 

                            </th>
                             <th width="3%" style="border: 1px solid; border-color: #ddd; !important">
                         <center>  Lower</center> 

                            </th>
                             <th width="3%" style="border: 1px solid ;  border-color: #ddd; !important">
                           <center>   Upper </center> 

                            </th>
                               <th width="3%" style="border: 1px solid;  border-color: #ddd; !important">
                         <center>  Lower</center> 

                            </th>
                             <th width="3%" style="border: 1px solid ;  border-color: #ddd; !important">
                           <center>   Upper </center> 

                            </th>
                       
                </tr>
                            </thead>
                            <tbody>

              <?php if (empty($DenominationHeatMap)) { ?>
                    <tr>
                        <td colspan='<?php echo $columns; ?>' class='text-warning'><?php echo __('No Configuration found.') ?></td>
                    </tr>
                <?php } else { ?>

                    <?php foreach ($DenominationHeatMap as $country): ?>
                        
                        <tr >

                        <td>
                                        <?php echo $startNo++; ?>
                                    </td>
                          <td class="table-text">
                                <?php    
                                 echo  ($country['DenominationHeatMap']['name']);
                                   ?>
                            </td>
                                <td class="table-text">
                                 <?php   if($country['DenominationHeatMap']['branch_id']==0){
                                  echo "All";
                                }else{
                                 echo ($country['CompanyBranches']['name']);
                                  } ?>
                                 
                                 
                            </td>
 
                          
                            <td>
                              <?php if($country['DenominationHeatMap']['machine_id']==0){
                                    echo "All";
                                } else{
                                   echo $country['DenominationHeatMap']['machine_id'];
                                  } ?>
                                
                            </td>
                              <td style="border: 1px solid ; border-color: #ddd; !important">
                              <center>   <?php echo $country['DenominationHeatMap']['1_lower']; ?> </center> </td> 
                              <td  style=" border: 1px solid;  border-color: #ddd; !important"><center> <?php echo $country['DenominationHeatMap']['1_upper']; ?></center> 
                                 
                            </td>
                               <td style="border: 1px solid ; border-color: #ddd; !important">
                              <center>   <?php echo $country['DenominationHeatMap']['2_lower']; ?> </center> </td> 
                              <td style="border: 1px solid;  border-color: #ddd; !important"><center> <?php echo $country['DenominationHeatMap']['2_upper']; ?></center> 
                                 
                            </td>
                             <td style="border: 1px solid ; border-color: #ddd; !important">
                              <center>   <?php echo $country['DenominationHeatMap']['5_lower']; ?> </center> </td> 
                              <td style="border: 1px solid;  border-color: #ddd; !important"><center> <?php echo $country['DenominationHeatMap']['5_upper']; ?></center> 
                                 
                            </td>
                             <td style="border: 1px solid ;  border-color: #ddd; !important">
                              <center>   <?php echo $country['DenominationHeatMap']['10_lower']; ?> </center> </td> 
                              <td style="border: 1px solid;  border-color: #ddd; !important"><center> <?php echo $country['DenominationHeatMap']['10_upper']; ?></center> 
                                 
                            </td>
                             <td style="border: 1px solid ;  border-color: #ddd; !important">
                              <center>   <?php echo $country['DenominationHeatMap']['20_lower']; ?> </center> </td> 
                              <td style="border: 1px solid;  border-color: #ddd; !important"><center> <?php echo $country['DenominationHeatMap']['20_upper']; ?></center> 
                                 
                            </td>
                             <td style="border: 1px solid ;  border-color: #ddd; !important">
                              <center>   <?php echo $country['DenominationHeatMap']['50_lower']; ?> </center> </td> 
                              <td style="border: 1px solid;  border-color: #ddd; !important"><center> <?php echo $country['DenominationHeatMap']['50_upper']; ?></center> 
                                 
                            </td>
                             <td style="border: 1px solid ;  border-color: #ddd; !important">
                              <center>   <?php echo $country['DenominationHeatMap']['100_lower']; ?> </center> </td> 
                               <td style="border: 1px solid;  border-color: #ddd; !important"><center> <?php echo $country['DenominationHeatMap']['100_upper']; ?></center> 
                                 
                            </td>

                               <td class="actions text-center">
                                <?php
                                $sessionData = getMySessionData();
                                     echo $this->Html->link('', array('action' => 'edit', encrypt($country['DenominationHeatMap']['id'])), array('icon' => 'icon-pencil5 edit', 'title' => __('Click here to edit this Heat Map')));
                                     if($country['DenominationHeatMap']['Is_default']!=1){
                                         echo $this->Html->link('', array('action' => 'delete', encrypt($country['DenominationHeatMap']['id'])), array('icon' => 'icon-trash delete', 'title' => __('Click here to delete this Heat Map')), __('Are you sure you want to delete Denomination Heat Map?'));
                                }
                                ?>
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
    <?php echo $this->Form->end(); ?>
</div>

                </div>


<script type="text/javascript">
    jQuery(document).ready(function () {
        validateSearch("CountrySearchForm", ["CountryName"]);
    });
</script>
