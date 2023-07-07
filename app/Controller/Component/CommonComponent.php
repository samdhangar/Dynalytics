<?php
class CommonComponent extends Component {
    var $components = array('Session');

    public function __construct(ComponentCollection $collection, $settings = array()) {
        $this->_controller = $collection->getController();
        parent::__construct($collection, $settings);
    }

    public function initialize(Controller $controller) {
        $this->controller = $controller;
    }
    
    function getActivationCode($userId, $emailAddress) {
        $hash = $userId . $emailAddress;
        return Security::hash($hash, 'sha256', true);
    }

    function getRandomToken($length = 10) {
        $token = "";
        for ($i = 0; $i < 100; $i++) {
            $d = rand(1, 100000) % 2;
            $d ? $token .= chr(rand(33, 79)) : $token .= chr(rand(80, 126));
        }
        (rand(1, 100000) % 2) ? $token = strrev($token) : $token = $token;

        // Generate hash of random string
        $hash = Security::hash($token, 'sha256', true);
        for ($i = 0; $i < $length; $i++) {
            $hash = Security::hash($hash, 'sha256', true);
        }
        return $hash;
    }

    function isSiteActive() {
        $status = Configure::read('Site.Status');
        if (strtolower($status) == 'inactive' && (strtolower($this->controller->params['controller']) == 'pages' && strtolower($this->controller->params['action']) != 'maintenance')) {
            return $this->controller->redirect('/maintenance.html');
        } elseif (strtolower($status) != 'inactive' && strtolower($this->controller->params['controller']) == 'pages' && strtolower($this->controller->params['action']) == 'maintenance') {
            return $this->controller->redirect('/');
        }
    }

    function setMeta($title, $description) {
        //Format Data
        $title = trim($title);
        $description = trim($description);
        $arrMeta = array(
            'keyword' => $title,
            'description' => $description,
            'og:title' => $title,
            'og:description' => strip_tags($description),
            'og:url' => Configure::read('Site.Url') . $this->controller->here,
            'og:site_name' => Configure::read('Site.Name'),
            'article:publisher' => Configure::read('Social.Facebook.Share'),
            'og:image' => Router::url('/', true) . 'img/logo.png',
            'twitter:site' => Configure::read('Social.Twitter.Share'),
        );
        $this->controller->set('meta', $arrMeta);
    }
}