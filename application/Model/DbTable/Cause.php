<?php
 /**
  * This is the DbTable class for the cause table,
  * the main table for the site.
  */
class Model_DbTable_Cause extends Zend_Db_Table_Abstract {
 /** Table name */
  protected $_name = 'causes';

  protected $dependentTables = array('Pie');

  public function create($name,$user) {
    if ((!$user) || ($user ==0)) return 0;
    $data = array(
      'title'=>$name,
      'user_id'=>$user
    );
    return $this->insert($data);
  }

  public function fetchAll($where = null, $order = null, $count = null, $offset = null) {
    //
//    $select = $this->select()
//                      ->from(array('pies','causes'),array('causes.*','pies.pie_id'))
//                      ->join('pies','pies.owner_id = causes.cause_id AND pies.owner_type = "CAUSE"',array())
//                      ->where($where);
//                      print_r ($select->assemble());
    return Zend_Db_Table_Abstract::fetchAll($where,$order,$count,$offset);
  }

  public function fetchCauses($where = null) {
    $select = "SELECT c.*, p.pie_id FROM causes AS c INNER JOIN pies AS p ON p.owner_id = c.cause_id AND p.owner_type = 'CAUSE'";
    if ($where) $select .= " WHERE ".$where;
    $causes = $this->getAdapter()->fetchAll($select);
    return $causes;
  }
} 
