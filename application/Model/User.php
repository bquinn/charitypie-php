<?php
/**
 * User table model - uses underlying DbTable class
 *
 */
class Model_User {
 /** Model_Table_User */
  protected $_table;
 /**
  * Retrieve table object
  *
  * @return Model_User_Table
  */
  public function getTable() {
    if (null === $this->_table) {
      $this->_table = new Model_DbTable_User;
    }
    return $this->_table;
  }

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
  *
  * @return Zend_Db_Table_Rowset_Abstract
  */
  public function fetchEntries() {
    // we are gonna return just an array of the data since
    // we are abstracting the datasource from the application,
    // at current, only our model will be aware of how to manipulate
    // the data source (dbTable).
	// We will obviously want to remove this as the table gets bigger!
    return $this->getTable()->fetchAll('1')->toArray();
  }

 /**
  * Fetch an individual user
  *
  * @param int|string $id
  * @return null|Zend_Db_Table_Row_Abstract
  */
  public function fetchUser($id) {
    $table = $this->getTable();
    $select = $table->select()->where('id = ?', $id);
    // see reasoning in fetchEntries() as to why we return only an array
    return $table->fetchRow($select)->toArray();
  }

 /**
  * Fetch an individual user by email address
  *
  * @param int|string $email
  * @return null|Zend_Db_Table_Row_Abstract
  */
  public function fetchUserByEmail($email) {
    $table = $this->getTable();
    $select = $table->select()->where('email = ?', $email);
    return $table->fetchRow($select)->toArray();
  }
} 
