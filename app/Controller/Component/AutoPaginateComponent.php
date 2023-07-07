<?php

class AutoPaginateComponent extends Component
{
    /**
     * Other components needed by this component
     *
     * @access public
     * @var array
     */
    public $components = array('Session');

    /**
     * component settings
     * 
     * @access public
     * @var array
     */
    public $settings = array();

    /**
     * Default values for settings.
     * - options: the results-per-page options to present to the user.
     *
     * @access private
     * @var array
     */
    private $__defaults = array(
        'options' => array(1, 5, 10, 25, 50, 100),
        'defaultLimit' => 25
    );

    public function __construct(ComponentCollection $collection, $settings = array())
    {
        $this->settings = array_merge($this->__defaults, $settings);
        parent::__construct($collection, $settings);
    }

    /**
     * Configuration method.
     *
     * @access public
     * @param object $model
     * @param array $settings
     */
    public function initialize(Controller $controller)
    {
        $this->controller = $controller;
    }

    /**
     * beforeRender()
     *
     * Set the variables needed by the controller.
     *
     * @access public
     * @param $controller Controller object
     */
    public function beforeRender(Controller $controller)
    {
        $controller->set('paginationOptions', $this->settings['options']);
        $controller->set('paginationLimit', $this->paginationLimit());
    }

    /**
     *
     * Set the controller's $paginate variable.
     *
     * @access public
     * @param array $options
     */
    public function setPaginate($options = array())
    {
        $defaults = array(
            'limit' => $this->paginationLimit()
        );
        $this->controller->paginate = array_merge($defaults, $options);
    }

    /**
     * Set the pagination limit based on user input and session variables.
     *
     * @access public
     */
    public function paginationLimit()
    {
        if (isset($this->controller->params['named']['Paginate'])) {
            $this->Session->write('Pagination.limit', $this->controller->params['named']['Paginate']);
        }
        return ($this->Session->check('Pagination.limit') ? $this->Session->read('Pagination.limit') : $this->settings['defaultLimit']);
    }
}
