<?php
 /**
  * This is the DbTable class for the user table, which stores credentials like email and password.
  */
class Model_DbTable_User extends Zend_Db_Table_Abstract {
 /** Table name */
  protected $_name = 'user';

  protected $dependentTables = array('Aggregation');

 /**
  * Insert new row
  *
  * Ensure that a timestamp is set for the created field.
  *
  * @param array $data
  * @return int
  */
  public function insert(array $data) {
    $data['registered_date'] = date('Y-m-d H:i:s');
    return parent::insert($data);
  }
} 
