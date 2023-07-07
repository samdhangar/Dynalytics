<?php
App::uses('AppModel', 'Model');

/**
 * CompanyBranch Model
 *
 * @property Company $Company
 */
class RecentReports extends AppModel
{

    //The Associations below have been created with all possible keys, those that are not needed can be removed
    public $actsAs = array(
        'Containable'
    );
    public $useTable = 'recent_reports';
    public $belongsTo = array();
}
                