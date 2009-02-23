<?php
 /**
  * This is the DbTable class for the pie table,
  * the main table for each user's pie.
  */
class Model_DbTable_Pie extends Zend_Db_Table_Abstract {
 /** Table name */
  protected $_name = 'pies';

  // protected $dependentTables = array('');

  public function fetchPieId($ownerId,$type) {
    $pie = $this->fetchRow('owner_id = '.$ownerId.' AND owner_type = "'.$type.'"');
    if (!$pie) return null;
    $pie->toArray();
    return $pie['pie_id'];
  }

  public function createPie($ownerId,$type) {
    $pieId = $this->fetchPieId($ownerId,$type);
    if (!$pie) {
      $data = array(
        'owner_id'=>$ownerId,
        'owner_type'=>$type
      );
      $pieId = $this->insert($data);
    }
    return $pieId;
  }
} 
