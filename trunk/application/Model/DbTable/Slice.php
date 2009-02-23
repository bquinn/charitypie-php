<?php
 /**
  * This is the DbTable class for the slice table,
  * representing a segment of a user's pie (or cause?)
  */
class Model_DbTable_Slice extends Zend_Db_Table_Abstract {
 /** Table name */
  protected $_name = 'pie_slices';

  // protected $dependentTables = array('');

  public function sliceId($pieId,$recipientId,$type)
  {
    $slice = $this->fetchRow('pie_id = '.$pieId.' AND recipient_id = '.$recipientId.' AND recipient_type = "'.$type.'"');
    if (empty($slice)) {
      return null;
    } else {
      $slice = $slice->toArray();
      return $slice['slice_id'];
    }
  }

  /**
   * Adds a charity to the specified pie, if the slice doesn't already exist.
   * 
   * @param int $pieId
   * @param int $charityId
   * @return int $sliceId
   * 
   */
  public function addSlice($pieId,$recipientId,$type)
  {
    $slice_id = $this->sliceId($pieId,$recipientId,$type);
    if (!$slice_id)
    {
      $data = array(
        'pie_id' => $pieId,
        'recipient_id' => $recipientId,
        'recipient_type' => $type
      );
      $slice_id = $this->insert($data);
    }
    return $slice_id;
  }

  public function deleteSlice($sliceId)
  {
    $this->delete('slice_id = '.$sliceId);
  }

  public function getPieSlices($pieid)
  {
    $slices = $this->getAdapter()->fetchAll("
      SELECT s.slice_id, s.size, s.recipient_id, s.recipient_type, COALESCE(c.name, x.title) as recipient_name
        FROM pie_slices s
          LEFT OUTER JOIN charities c ON (s.recipient_id = c.charity_id AND s.recipient_type = 'CHARITY')
          LEFT OUTER JOIN causes x ON (s.recipient_id = x.cause_id AND s.recipient_type = 'CAUSE')
        WHERE s.pie_id = ".$pieid);
    return $slices;
  }

  public function getPieSlicesCount($pieId)
  {
    $slices = $this->fetchAll('pie_id = '.$pieId);
    return (count($slices));
  }
} 
