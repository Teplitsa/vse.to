<?php defined('SYSPATH') or die('No direct script access.');

class DbTable_Config extends DbTable {

    protected $_columns = array(
        'group_name'    => array('Type' => 'varchar(63)'),
        'config_key'    => array('Type' => 'varchar(63)'),
        'config_value'  => array('Type' => 'text'),
    );

    protected $_indexes = array(
        'PRIMARY'       => array('Type' => 'PRIMARY', 'Column_names' => array('group_name', 'config_key'))
    );
}