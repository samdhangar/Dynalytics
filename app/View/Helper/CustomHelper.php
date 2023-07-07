<?php
App::uses('HtmlHelper', 'View/Helper');

class CustomHelper extends HtmlHelper
{
    public $helpers = array('Html', 'Text');

    /**
     * This function is use to display inline warning to user
     * */
    public function showInfo($message = '', $options = array())
    {
        $defaultOptions = array(
            'close' => false,
            'type' => 'success',
        );
        $options = array_merge($defaultOptions, $options);
        $messageBody = '<div class="showinfo alert alert-' . $options['type'] . ' fade in" style="margin:13px;">';
        if ($options['close']) {
            $messageBody .= '<button class="close" data-dismiss="alert">X</button>';
        }
        $messageBody .= $message;
        $messageBody.='</div>';
        return $messageBody;
    }

    public function getActiveClass($controller, $action = 'index')
    {
        if (isset($this->request->params['controller']) && $this->request->params['controller'] == $controller) {
            if ($this->action == $action) {
                return 'active';
            }
        }
    }

    public function showAmount($price, $currency = '$ ')
    {
        return $currency . (floatval($price));
    }

    public function getCrumbs($separator = '', $startText = false)
    {
        $options['separator'] = $separator;
        $options['class'] = 'breadcrumb';
        $options['escape'] = false;
        $startText = !empty($startText) ? $startText : '<i class="fa fa-dashboard"></i> Home';
        return parent::getCrumbList($options, $startText);
    }

    public function addCrumb($name, $link = null, $options = null)
    {
        if (isset($options['title'])) {
            $name = "<span title='" . $options['title'] . "'>$name</span>" ;
        }
        parent::addCrumb($name, $link, $options);
    }

    function link($title, $href = null, $options = array(), $confirm = null, $escapeTitle = true)
    {
        if (!empty($options['span'])) {
            $title = '<span>' . $title . '</span>';
            $options['escape'] = false;
        }
        if (!empty($options['hasSubMenu'])) {
            $title .= '<i class="fa fa-angle-left pull-right"></i>';
            $options['escape'] = false;
        }

        if (!empty($options['icon'])) {
            $title = '<i class="' . $this->getIcon($options['icon']) . '">&nbsp;</i>' . $title;
            $options['escape'] = false;
        }
        if (is_array($href) && !isset($href['plugin'])) {
            $href['plugin'] = false;
        }
        return parent::link($title, $href, $options, $confirm, $escapeTitle);
    }

    function getIcon($name = "")
    {
        $arrIcon = array(
            'add' => 'fa-plus',
            'edit' => 'fa-edit',
            'delete' => 'fa-trash-o',
            'view' => 'fa-th',
            'back' => 'fa-angle-double-left',
            'cancel' => 'fa-times',
        );
        $icon = isset($arrIcon[strtolower($name)]) ? $arrIcon[strtolower($name)] : $name;
        return 'fa ' . $icon;
    }

    public function showStatus($status = 'pending')
    {
        return Inflector::humanize($status);
    }

    public function cropDetail($text = 'here', $length = 60)
    {
        return $this->Text->truncate($text, $length, array('ellipsis' => '...', 'exact' => false, 'html' => false));
    }

    public function getToggleButton($currValue = 'active', $class = "userStatusChange", $otherOpt = array(), $status = array('active', 'inactive'))
    {
        $activeStatus = $status[0];
        $upperActive = ucfirst($activeStatus);
        $inactiveStatus = $status[1];
        $upperInActive = ucfirst($inactiveStatus);
        $toggleClass = (($currValue != $activeStatus) ? ' btn-danger off ' : ' btn-success ');
        $active = (($currValue == $activeStatus) ? " $activeStatus " : '');
        $inactive = (($currValue != $activeStatus) ? " $activeStatus " : '');
        $html = "<div class='$class toggle btn btn-xs $toggleClass' style='width: 59px; height: 22px;' ";
        foreach ($otherOpt as $key => $value) {
            $html .= ' ' . $key . '="' . $value . '" ';
        }
        $html .= "><div class='toggle-group'>" .
            "<label class='btn btn-success btn-xs toggle-on $active '>$upperActive</label>" .
            "<label class='btn btn-danger btn-xs toggle-off $inactive '>$upperInActive</label>" .
            "<span class='toggle-handle btn btn-default btn-xs'> </span>" .
            "</div>" .
            "</div>";
        return $html;
    }

    function displayAddress($fullAddress = array())
    {
//        $html = "<dt>" . __('Address') . " :</dt>";
        $html = '';
        $html .= $fullAddress['address'];
        if (!empty($fullAddress['city']) && $fullAddress['state']):
            $html .= "<br>" . (!empty($fullAddress['city']) ? $fullAddress['city'] . ' , ' : '') . $fullAddress['state'];
        endif;
        $html .= "<br>" . (!empty($fullAddress['country']) ? $fullAddress['country'] . ' - ' : '') . $fullAddress['pincode'];
//        $html .= " </dd>";
        return $html;
    }

    function displayDiffDate($firstDate = null, $lastDate = null)
    {
        $startDate = new DateTime($firstDate);
        $endDate = new DateTime($lastDate);
        $dateDiff = date_diff($startDate, $endDate);
        $year = $month = $day = $hour = $min = $sec = 0;
        $text = '';
        if ($dateDiff->y) {
            $year = $dateDiff->y;
            if (!empty($year)) {
                $text .= $dateDiff->y . ' year';
            }
        }
        if ($dateDiff->m) {
            $month = $dateDiff->m;
            if (!empty($month)) {
                $text = (!empty($text)) ? $text . ', ' : '';
                $text .= $dateDiff->m . ' month';
            }
        }
        if ($dateDiff->d) {
            $day = $dateDiff->d;
            if (!empty($day)) {
                $text = (!empty($text)) ? $text . ', ' : '';
                $text .= $dateDiff->d . ' day';
            }
        }
        if ($dateDiff->h) {
            $hour = $dateDiff->h;
            if (!empty($hour)) {
                $text = (!empty($text)) ? $text . ', ' : '';
                $text .= $dateDiff->d . ' hour';
            }
        }
        if ($dateDiff->i) {
            $min = $dateDiff->i;
            if (!empty($min)) {
                $text = (!empty($text)) ? $text . ', ' : '';
                $text .= $dateDiff->d . ' minute';
            }
        }
        if ($dateDiff->s) {
            $sec = $dateDiff->s;
            if (!empty($sec)) {
                $text = (!empty($text)) ? $text . ', ' : '';
                $text .= $dateDiff->d . ' second';
            }
        }
        return $text;
    }
}
