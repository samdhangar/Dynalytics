<?php
Router::connect('/', array('controller' => 'users', 'action' => 'login'));
Router::connect('/pages/contact-us', array('controller' => 'pages', 'action' => 'contact_us'));
Router::connect('/pages/comming-soon', array('controller' => 'pages', 'action' => 'unknown'));
Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'view'));
Router::connect('/maintenance.html', array('controller' => 'pages', 'action' => 'maintenance'));
Router::parseExtensions('csv');
Router::parseExtensions('pdf');
/**
 * Load all plugin routes. See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
CakePlugin::routes();

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
require CAKE . 'Config' . DS . 'routes.php';
