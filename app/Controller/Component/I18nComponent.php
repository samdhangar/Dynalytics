<?php 
/*
 * Class name :I18nComponent
 * purpose : To Handle Request of Language 
*/
class I18nComponent extends Component {
    var $components = array('Session', 'Cookie');
    
	public function __construct(ComponentCollection $collection, $settings = array()) {
        $this->settings = $settings;
        parent::__construct($collection, $settings);        
    }
    function startup(\Controller $controller) {
        parent::startup($controller);
        if (!$this->Session->check('Config.language')) {
            $this->change(($this->Cookie->read('lang') ? $this->Cookie->read('lang') : DEFAULT_LANGUAGE));
        }
    }

    function change($lang = null) {
        if (!empty($lang)) {
            $this->Session->write('Config.language', $lang);
            $this->Cookie->write('lang', $lang, null, '+350 day'); 
        }
    }
}
?> 
