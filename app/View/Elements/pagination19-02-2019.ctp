<?php if ($this->Paginator->hasPage()) { ?>
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
            $urlParams = '';
            if (!empty($this->Paginator->params['pass'])) {
                $urlParams = implode('/', $this->Paginator->params['pass']);
                $urlParams .= '/';
                $urlParams = urlencode($urlParams);
            }
            if (!empty($this->Paginator->params['named'])) {
                foreach ($this->Paginator->params['named'] as $key => $value):
                    if (!in_array($key, array('page', 'Model'))) {
                        $urlParams .= $key . ':' . $value . '/';
                    }
                endforeach;
            }
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
                'url' => urlencode($urlParams . 'Model:' . $paginateModel),
                'model' => $paginateModel
            ));
        }
        echo $this->Paginator->prev('&laquo; Previous', $options, null, array('escape' => false, 'tag' => 'li', 'disabledTag' => 'a'));
        echo $this->Paginator->numbers($countArr);
        echo $this->Paginator->next('Next &raquo;', $options, null, array('escape' => false, 'tag' => 'li', 'disabledTag' => 'a'));

        ?>
    </ul>
<?php } ?>