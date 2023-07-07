<?php
App::uses('AppModel', 'Model');

/**
 * EmailTemplate Model
 *
 */
class OutlookAccessToken extends AppModel
{
    /**
     * Display field
     *
     * @var string
     */
    public $useTable = 'outlook_access_token';
    public $displayField = 'name';

}
