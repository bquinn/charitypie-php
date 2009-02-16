<?php
/**
 * Abstract class for a data model - sits on top of the appropriate DbTable class,
 * abstracts the DB-specific stuff away from other parts of the system
 *
 */
abstract class Model_Abstract {

 /** Model_DbTable */
  protected $_table;

 /** there was a getTable function, but as that contains the name of the DbTable class
  * we need to create it for each child class -- can probably parameterise it somehow
  * though??
  */

 /**
  * Save a new entry
  *
  * @param array $data
  * @return int|string
  */
  public function save(array $data) {
    $table = $this->getTable();
    $fields = $table->info(Zend_Db_Table_Abstract::COLS);
    foreach ($data as $field => $value) {
      if (!in_array($field, $fields)) {
        unset($data[$field]);
      }
    }
    return $table->insert($data);
  }

 /**
  * Fetch all entries
  * (might want to get rid of this? It's pretty dangerous when we have big tables...)
  *
  * @return Zend_Db_Table_Rowset_Abstract
  */
  public function fetchAll() {
	// hide the DB structure from the caller, only return an array
    return $this->getTable()->fetchAll('1')->toArray();
  }

 /**
  * Fetch an individual entry
  *
  * @param int|string $id
  * @return null|Zend_Db_Table_Row_Abstract
  */
  public function fetchById($id) {
    $table = $this->getTable();
    $select = $table->select()->where('id = ?', $id);
	// hide the DB structure from the caller, only return an array
    return $table->fetchRow($select)->toArray();
  }
} 
