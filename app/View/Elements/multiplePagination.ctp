<ul class="pagination pagination-sm no-margin pull-right">
    <?php
    if (!isset($options)) {
        $options = array();
    }
    if (!isset($countArr)) {
        $countArr = array();
    }
    $options = array_merge($options, array(
        'escape' => false,
        'tag' => 'li'
    ));
    $countArr = array_merge($countArr, array(
        'separator' => '',
        'tag' => 'li',
        'currentTag' => 'a',
        'currentClass' => 'active'
    ));
    if (!empty($paginateModel)) {
        $extraParamter = isset($extraParamter) ? $extraParamter : '';
        $options = array_merge($options, array(
            'escape' => false,
            'url' => array(
                'Model' => $paginateModel
            ),
            'model' => $paginateModel
        ));

        $countArr = array_merge(
            $countArr, array(
            'escape' => false,
            'url' => array($extraParamter,'model' => $paginateModel, 'Model' => $paginateModel, 'Paginate' => isset($this->request->named['Paginate']) ? $this->request->named['Paginate'] : 20),
            'model' => $paginateModel
        ));
    }
    echo $this->Paginator->prev('&laquo; Previous', $options, null, array('escape' => false, 'tag' => 'li', 'disabledTag' => 'a'));
    echo $this->Paginator->numbers($countArr);
    echo $this->Paginator->next('Next &raquo;', $options, null, array('escape' => false, 'tag' => 'li', 'disabledTag' => 'a'));

    ?>
</ul>