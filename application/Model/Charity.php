<?php
/**
 * This model class represents the business logic associated with a "guestbook"
 * model. While its easy to say that models are generally derived from
 * database tables, this is not always the case. Data sources for models are
 * commonly web services, the filesystem, caching systems, and more. That
 * said, for the purposes of this guestbook applicaiton, we have split the
 * buisness logic from its datasource (the dbTable).
 *
 * This particular class follows the Table Module pattern. There are other
 * patterns you might want to employ when modeling for your application, but
 * for the purposes of this example application, this is the best choice.
 * To understand different Modeling Paradigms:
 *
 * @see http://martinfowler.com/eaaCatalog/tableModule.html [Table Module]
 * @see http://martinfowler.com/eaaCatalog/ [See Domain Logic Patterns and Data Source Arch. Patterns]
 */
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
  public function fetchByCharityId($charityid) {
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
  * Fetch a set of aggregations by user's email address
  * (which should be unique anyway)
  * (we use the email address because that's the unique key in Zend_Auth
  * so we don't have to look it up in the DB, wasting cycles...)
  *
  * @param int|string $email
  * @return null|Zend_Db_Table_Row_Abstract
  * @throws MyCharityPie_CharityDoesNotExistException
  */
  public function fetchByEmail($email) {
	$table = $this->getTable();
	$select = $table->select()->setIntegrityCheck(false)
						->from('aggregation')
						->where('user.email = ?', $email)
						->join('user', 'user.id = aggregation.creator_id')
						->order(array('aggregation.agg_name DESC'));
	$result = $table->fetchRow($select);
	if ($result) {
		// "hide the DB structure from the caller, only return an array"
		// (I'm beginning to wear thin of that approach...? how about DAOs?)
		return $result->toArray();
	} else {
		return "";
	}
  }

 /**
  * Fetch matching Items (via AggFeeds)
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
