<?php
 /**
  * This is the DbTable class for the charity table,
  * the main table for the site.
  */
class Model_DbTable_Charity extends Zend_Db_Table_Abstract {
 /** Table name */
  protected $_name = 'charities';

  protected $dependentTables = array('Model_DbTable_CharityLabel');

  /**
   * Returns a subset of tags that are actually used by charities
   */
  public function fetchAllCharityTags() {
    return $this->getAdapter()->fetchAll("
      SELECT t.tag_id, t.label, COUNT(t.tag_id) AS charities
      FROM charity_label AS cl
      INNER JOIN tags AS t ON cl.tag_id = t.tag_id
      GROUP BY cl.tag_id");
  }

  /**
   * Retruns all categories with a charity count
   * @return array
   */
  public function fetchAllCharityCategories() {
    return $this->getAdapter()->fetchAll("
      SELECT c.category_id, c.label, COUNT(cc.charity_id) AS charities
      FROM categories AS c
      INNER JOIN categories_charities AS cc ON c.category_id = cc.category_id
      GROUP BY c.category_id");
  }

  public function fetchCharitiesBySearch($searchStr) {
    $searchStr = '%'.$searchStr.'%';
    return $this->getAdapter()->fetchAll("SELECT * FROM charities AS c WHERE c.name LIKE ?",$searchStr);
  }

  public function fetchCharitiesByTag($tagId) {
    return $this->getAdapter()->fetchAll("SELECT * FROM charities AS c INNER JOIN charity_label AS cl ON c.charity_id = cl.charity_id WHERE cl.tag_id = ?",$tagId);
  }

  public function fetchCharitiesByCategory($categoryId) {
    return $this->getAdapter()->fetchAll("SELECT * FROM charities AS c INNER JOIN categories_charities AS cc ON c.charity_id = cc.charity_id WHERE cc.category_id = ?",$categoryId);
  }

  public function fetchCharityById($charityid) {
    $select = $this->select()->setIntegrityCheck(false)
              ->from('charities')
              ->where('charity_id = ?', $charityid);
    $result = $this->fetchRow($select);
    if ($result) {
      // hide the DB structure from the caller, only return an array
      return $result->toArray();
    } else {
      throw new MyCharityPie_CharityDoesNotExistException("Charity ".$charityid." doesn't exist in the database.");
    }
  }

} 
