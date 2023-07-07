<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MachineError
 *
 * @author securemetasys002
 */
App::uses('AppModel', 'Model');

class ErrorType extends AppModel
{
    public $actsAs = array(
        'Containable'
    );
}
