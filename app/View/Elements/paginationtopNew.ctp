<div class='row pagination-body' style="<?php echo (empty($pagination) ? '' : $pagination) ?>">

    <?php //print_r($top_left); ?>
    <?php if (empty($from)) { ?>
        <?php
		if(!empty($navId)){
			$this->Paginator->options(array('url' => array('#' => $navId)));
		}
        if (!isset($options)) {
            $options = array();
        }
        $options['format'] = 'Displaying {:start2} - {:end} of  {:count} total';
        if (!empty($paginateModel)) {
            $options['model'] = $paginateModel;
        }

        ?>
        <div class="col-md-6 text-left pagination-info">
            <?php
//            $topLine = $this->Paginator->counter($options);
//            if ($topLine == 'Displaying 0 - of total') {
////                $topLine = __('Displaying 0 - 0 of total 0');
//            }
            $end=$top_left['start']+$top_left['end'];
            if($end>$top_left['total']){
                $end=$top_left['total'];
            }

            echo "Displaying ".($top_left['start']+1)."-".$end." of ".$top_left['total'];
           // echo $this->Paginator->counter($options);;

            ?>
        </div>
        <?php } ?>
    <div class="col-md-6 text-right pagination-info">
        <?php
        echo __("Page Size: ");
        $results = array();
        foreach ((array) $paginationOptions as $option) {
            if ($paginationLimit == $option) {
                $results[] = $option;
            } else {
                $args = $this->passedArgs;
                if (!empty($paginateModel)) {
                    $args['Model'] = $paginateModel;
                }
                $args['Paginate'] = $option;
                $results[] = $this->Html->link($option, $args);
            }
        }
        
        echo implode(" | ", $results);

        ?>
    </div>
</div>
