<?php
class CkHelper extends AppHelper {
	
	
    var $helpers = Array('Html', 'Js','Session');
    
    function load($id,$toolbar='Full',$height='200px') {
        $did = $this->Html->domId($id);
        $code = "CKEDITOR.replace( '".$did."',{toolbar : '".$toolbar."',height:'".$height."'} );";
        return $this->Html->scriptBlock($code);
    }
}
?>
