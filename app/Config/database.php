<?php

class DATABASE_CONFIG
{
public $default = array(
        'datasource' => 'Database/Mysql',
        'persistent' => false,
        'host' => 'localhost',
        'login' => 'root',
        'password' => '',
        'database' => 'dynalytics',
        'prefix' => '',
        'encoding' => 'utf8',
    );    
public $test = array(
        'datasource' => 'Database/Mysql',
        'persistent' => false,
        'host' => 'localhost',
        'login' => 'root',
        'password' => '',
        'database' => 'cake2',
        'prefix' => '',
        'encoding' => 'utf8',
    );

} 
