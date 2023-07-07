<?php 
if(!empty($addData)):
    ?>
    <option value=""><?php echo $addComboTitle; ?></option>
    <?php
    foreach ($addData as $key=>$value): ?>
        <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
    <?php endforeach;
else: ?>
    <option value=""><?php echo $addDataTitle; ?></option>
<?php 
endif;
?>