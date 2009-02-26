<?php
 /**
  * This is the DbTable class for the cause table,
  * the main table for the site.
  */
class Model_DbTable_Cause extends Zend_Db_Table_Abstract {
 /** Table name */
  protected $_name = 'causes';

  // protected $dependentTables = array('');

  public function create($name,$user) {
    if ((!$user) || ($user ==0)) return 0;
    $data = array(
      'title'=>$name,
      'user_id'=>$user
    );
    return $this->insert($data);
  }
} 
