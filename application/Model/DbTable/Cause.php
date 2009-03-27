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

  /**
   * Returns a Zend_Db_Select for fetching all causes for the specified tag
   * @param <type> $tagid
   * @return <type>
   */
  public function getCausesByTagSelect($tagid) {
    $select = new Zend_Db_Select($this->getAdapter());
    $select->from(array('c'=>'causes'))
           ->join(array('cl'=>'cause_label'),'cl.cause_id = c.cause_id')
           ->joinLeft(array('s'=>'pie_slices'),
              's.recipient_id = c.cause_id AND s.recipient_type = "CAUSE"',
              array('followers'=>'COUNT(s.slice_id)')
           )
           ->where('cl.tag_id = ?',$tagid)
           ->group('c.cause_id');
    return $select;
  }

  public function fetchCauseDetails($causeId) {
    $select = new Zend_Db_Select($this->getAdapter());
    $select->from(array('c'=>'causes'))
           ->joinLeft(array('s'=>'pie_slices'),
              's.recipient_id = c.cause_id AND s.recipient_type = "CAUSE"',
              array('followers'=>'COUNT(s.slice_id)')
           )
           ->where('c.cause_id = ?',$causeId)
           ->group('c.cause_id');

    $return = $this->getAdapter()->fetchRow($select);

    return $return;
  }

  public function fetchMostPopularCauses() {
    $select = new Zend_Db_Select($this->getAdapter());
    $select->from(array('c'=>'causes'))
           ->joinLeft(array('s'=>'pie_slices'),
              's.recipient_id = c.cause_id AND s.recipient_type = "CAUSE"',
              array('followers'=>'COUNT(s.slice_id)')
           )
           ->group('c.cause_id')
           ->order('followers DESC')
           ->limit(5);

    $causes = $this->getAdapter()->fetchAll($select);

    return $causes;
  }

  public function fetchCauses($where = null) {
    $select = "SELECT c.*, p.pie_id FROM causes AS c INNER JOIN pies AS p ON p.owner_id = c.cause_id AND p.owner_type = 'CAUSE'";
    if ($where) $select .= " WHERE ".$where;
    $causes = $this->getAdapter()->fetchAll($select);
    return $causes;
  }

  /**
   * Return all tags with cause count
   */
  public function fetchAllTags() {
    $select = "SELECT *, COUNT(cl.cause_id) as causes FROM tags AS t
                INNER JOIN
                  (SELECT DISTINCT tag_id, cause_id FROM cause_label) AS cl
                ON cl.tag_id = t.tag_id
                GROUP BY t.tag_id";
    $return = $this->getAdapter()->fetchAll($select);
    return $return;
  }

  public function fetchCauseTags($causeId) {
//    $select = new Zend_Db_Table_Select($this);
//    $select->from(array('t'=>'tags'))
//           ->join(array('cl'=>'cause_label'),'cl.tag_id = t.tagid')
//           ->group('t.tagid')
//           ->where('cl.cause_id = ?',$causeId);
    $select = " SELECT t.*, COUNT(cl.cause_id) as 'count'
                FROM tags as t
                INNER JOIN cause_label AS cl
                  ON cl.tag_id = t.tag_id
                WHERE cl.cause_id = ".$causeId."
                GROUP BY t.tag_id";
    $return = $this->getAdapter()->fetchAll($select);
    return $return;
  }

  public function fetchUserCauseTags($causeId,$userId) {
    $select = " SELECT t.*
                FROM tags as t
                INNER JOIN cause_label AS cl
                  ON cl.tag_id = t.tag_id
                WHERE cl.cause_id = ".$causeId." AND cl.user_id = ".$userId;
    $return = $this->getAdapter()->fetchAll($select);
    return $return;
  }

  public function tagCause($tag,$causeId,$userId) {
    $select = "SELECT * FROM tags WHERE tags.label = '".$tag."'";
    $existing = $this->getAdapter()->fetchAll($select);

    /* Get an id for the requested tag */
    if (empty($existing)) {
      $this->getAdapter()->insert('tags',array(
        'label'=>$tag
      ));
      $tagId = $this->getAdapter()->lastInsertId('tags');
    } else {
      $tagId = $existing[0]['tag_id'];
    }

    /* Tag the cause */
    $this->getAdapter()->insert('cause_label',array(
      'tag_id'=>$tagId,
      'cause_id'=>$causeId,
      'user_id'=>$userId
    ));
  }

  public function removeTag($tagId,$causeId,$userId) {
    $where = 'tag_id='.$tagId.' AND cause_id='.$causeId.' AND user_id='.$userId;
    $this->getAdapter()->delete('cause_label',$where);
  }

  /**
   * Returns number of users tagging this cause
   * @param <type> $causeId
   */
  public function fetchTaggingUserCount($causeId) {
    $select = new Zend_Db_Select($this->getAdapter());

    $select->from(array('cl'=>'cause_label'))
           ->where('cl.cause_id = ?',$causeId)
           ->group('cl.user_id');

    $result = $this->getAdapter()->fetchAll($select->assemble());

    return (count($result));
  }
} 
