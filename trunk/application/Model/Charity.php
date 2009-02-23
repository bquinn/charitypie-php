<?php
class Model_Charity extends Model_Abstract {

 /**
  * Retrieve table object
  *
  * @return Model_DbTable_Charity
  */
  public function getTable() {
	// the class variable $_table is defined in the abstract superclass
	if (null === $this->_table) {
	  $this->_table = new Model_DbTable_Charity;
	}
	return $this->_table;
  }

 /**
  * Fetch an individual charity by site id
  *
  * @param int|string $siteid
  * @return null|Zend_Db_Table_Row_Abstract
  * @throws MyCharityPie_CharityDoesNotExistException
  */
  public function fetchCharityById($charityid) {
	$table = $this->getTable();
	$select = $table->select()->setIntegrityCheck(false)
						->from('charities')
						->where('charity_id = ?', $charityid);
	$result = $table->fetchRow($select);
	if ($result) {
		// hide the DB structure from the caller, only return an array
		return $result->toArray();
	} else {
		throw new MyCharityPie_CharityDoesNotExistException("Charity ".$siteid." doesn't exist in the database.");
	}
  }

 /**
  * Fetch an individual charity by site id
  *
  * @param int|string $siteid
  * @return null|Zend_Db_Table_Row_Abstract
  * @throws MyCharityPie_CharityDoesNotExistException
  */
    public function fetchCharitiesByCategory($categoryId) {
      $table = $this->getTable();
      $select = $table->select()->setIntegrityCheck(false)
                ->from('charities')
                ->where('category_id = ?', $categoryid);
      $result = $table->fetchAll($select);
      if ($result) {
        // hide the DB structure from the caller, only return an array
        return $result->toArray();
      } else {
        throw new MyCharityPie_CharityCategoryDoesNotExistException("Charity Category ".$categoryid." doesn't exist in the charities list.");
      }
  }
}
