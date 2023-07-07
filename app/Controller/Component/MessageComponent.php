<?php
class MessageComponent extends Component {  
	var $controller = null;
	var $components = array('Session');
	var $settings = array();
	var $success = null;
	var $warning = null;
	public function __construct(ComponentCollection $collection, $settings = array()) {
		$this->settings = array_merge(array('success' => 'success', 'warning' => 'warning'), $settings);		
		$this->success = $this->settings['success'];
		$this->warning = $this->settings['warning'];
        parent::__construct($collection, $settings);        
    }
	function initialize(Controller $controller){
		$this->controller = $controller;		
	}
	function setSuccess($msg, $url = null){
		$this->Session->setFlash(__($msg, true), $this->success);
		if (!empty($url)){
			$this->controller->redirect($url, null, true); 
		}
	}
	function setWarning($msg, $url = null){
		$this->Session->setFlash(__($msg, true), $this->warning);
		if (!empty($url)){
			$this->controller->redirect($url, null, true); 
		}
	}  
}
