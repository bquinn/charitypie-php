<?php
/**
 * Tag table model
 */
class Model_Tag {
 /** Model_Table_User */
  protected $_table;
 /**
  * Retrieve table object
  *
  * @return Model_User_Table
  */
  public function getTable() {
    if (null === $this->_table) {
      require_once APPLICATION_PATH . '/Model/DbTable/Tag.php';
      $this->_table = new Model_DbTable_Tag;
    }
    return $this->_table;
  }

  /**
   *
   */
  public function fetchById($id) {
    $table = $this->getTable();
    $select = $table->select()->where('tag_id = ?', $id);
    return $table->fetchRow($select)->toArray();
  }
} 
