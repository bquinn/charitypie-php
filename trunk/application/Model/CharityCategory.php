<?php
class Model_CharityCategory extends Model_Abstract {

 /**
  * Retrieve table object
  *
  * @return Model_DbTable_Charity
  */
  public function getTable() {
	// the class variable $_table is defined in the abstract superclass
	if (null === $this->_table) {
	  $this->_table = new Model_DbTable_CharityCategory;
	}
	return $this->_table;
  }

 /**
  * Fetch an individual category by id
  *
  * @param int|string $id
  * @return null|Zend_Db_Table_Row_Abstract
  * @throws MyCharityPie_CharityDoesNotExistException
  */
  public function fetchById($categoryid) {
	$table = $this->getTable();
	$select = $table->select()->setIntegrityCheck(false)
						->from('charity_category')
						->where('category_id = ?', $categoryid);
	$result = $table->fetchRow($select);
	if ($result) {
		// hide the DB structure from the caller, only return an array
		return $result->toArray();
	} else {
		throw new MyCharityPie_CategoryDoesNotExistException("Category ".$siteid." doesn't exist in the database.");
	}
  }

  /* Fetch list of categories 
  *
  * @return null|Zend_Db_Table_Row_Abstract
  */
  public function fetchCategoryList() {
	$table = $this->getTable();
	$select = $table->select()->setIntegrityCheck(false)
						->from('charity_category')
						->order(array('name ASC'));
	$categories = $table->fetchAll($select);
	$cats = $categories->toArray();
	return $cats;
  }

  /* Fetch matching 
  *
  * @param int|string $id
  * @return null|Zend_Db_Table_Row_Abstract
  */
  public function fetchItemsBySiteId($site_id) {
	$table = $this->getTable();
	$select = $table->select()->setIntegrityCheck(false)
						->from('aggregation')
						->where('aggregation.site_id = ?', $site_id)
						->join('agg_feed', 'aggregation.id = agg_feed.agg_id')
						->join('feed', 'agg_feed.feed_id = feed.id')
						->join('item', 'feed.id = item.feed_id')
						->order(array('item.item_pub_date DESC'));
	$feeds = $table->fetchAll($select);
	// hide the user from the DB rules
	$items = $feeds->toArray();
	return $items;
  }
}
