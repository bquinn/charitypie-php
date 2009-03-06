<?php
 /**
  * Authors: nathan@yura.net
  * Date:    26/02/09
  *
  * IMPORTANT: The slice model doesn't always work directly on the DB.
  * Theres is a session buffer in place so that any actions (delete,add)
  * are added to the session in a pie_id context.
  *
  * The session data is saved to the table by savePieSlices, and loaded from
  * the table using loadPieSlices
  *
  */
class Model_DbTable_Slice extends Zend_Db_Table_Abstract {

  protected $_name = 'pie_slices';
  protected $_pies = array();

  // protected $dependentTables = array('');

  /**
   * on init. load any pies saved to the session
   */
  public function init() {
    $session_pies = new Zend_Session_Namespace('pies');
    $this->_pies = $session_pies->pies;
  }

  /**
   * Adds a charity to the specified pie, if the slice doesn't already exist.
   * 
   * @param int $pieId
   * @param int $charityId
   * @return int $sliceId
   * 
   */
  public function addSlice($pieId,$recipientId,$type,$size = 0)
  {
    $slices = $this->getPieSlices($pieId);
    $slice_id = null;

    // Check for existing slice
    if ($slices && (!empty($slices))) {
      $i = 0;
      foreach ($slices as $slice) {
        if (($slice['recipient_type'] == $type) &&
            ($slice['recipient_id'] == $recipientId)) {
            $slice_id = $slice['slice_id'];
            // If found update the size
            $slices[$i]['size'] = $size;
        }
        $i++;
      }
    }

    // If slice not found, add it.
    if ($slice_id == null)
    {
      // Need dummy slice id for referencing later on (eg. to remove it)
      $slice_id = $pieId.'-'.(count($slices)+1);

      if ($type == 'CAUSE') {
        $cause = $this->getAdapter()->fetchRow('SELECT * FROM causes WHERE cause_id = '.$recipientId);
        $name = $cause['title'];
      } else {
        $charity = $this->getAdapter()->fetchRow('SELECT * FROM charities WHERE charity_id = '.$recipientId);
        $name = $charity['name'];
      }

      $data = array(
        'slice_id' => $slice_id,
        'pie_id' => $pieId,
        'recipient_id' => $recipientId,
        'recipient_type' => $type,
        'recipient_name' => $name,
        'size' => 0
      );

      array_push($slices,$data);
    }
    $this->_setPieSlices($pieId,$slices,true);
  }

  function updateSlice($pieId,$sliceId,$size) {
    $slices = $this->getPieSlices($pieId);
    if ($slices && (!empty($slices))) {
      $i = 0;
      foreach ($slices as $slice) {
        if ($slice['slice_id'] == $sliceId) {
            $slices[$i]['size'] = $size;
        }
        $i++;
      }
    }
    $this->_setPieSlices($pieId,$slices,true);
  }

  /**
   * Return the id of the pie owning $sliceId.
   * If this is a session slice the id is part of $sliceId.
   * 
   * @param <type> $sliceId
   * @return <type>
   */
  public function getSlicePieId($sliceId) {
    if (strpos($sliceId,'-') === false) {
      $slice = $this->fetchRow('slice_id = '.$sliceId)->toArray();
      $pieId = $slice['pie_id'];
    } else {
      $pieId = substr($sliceId,0,strpos($sliceId,'-'));
    }
    return $pieId;
  }

  /**
   * Remove a slice from a session stored pie
   *
   * @param <type> $sliceId
   */
  public function deleteSlice($sliceId)
  {
    $pieId = $this->getSlicePieId($sliceId);
    $slices = $this->getPieSlices($pieId);

    $i = 0;
    foreach ($slices as $slice) {
      if ($slice['slice_id'] == $sliceId) unset($slices[$i]);
      $i++;
    }
    $slices = array_values($slices);

    $this->_setPieSlices($pieId,$slices,true);
  }

  /**
   * Return the slices of a session stored pie
   * 
   * @param <type> $pieId
   * @return <type>
   */
  public function _getPieSlices($pieId) {
    $pie = $this->_pies[$pieId];
    if ($pie) {
      // If the pie has no slices make sure an empty array is returned.
      return ($pie['slices']) ?  $pie['slices'] : array();
    }
    return null;
  }

  /**
   * Set the slices of a session stored pie
   * 
   * @param <type> $pieId
   * @param <type> $slices
   * @param <type> $changed
   */
  protected function _setPieSlices($pieId,$slices,$changed) {
    $pie = array(
      'changed'=>$changed,
      'slices'=>$slices
      );

    $this->_pies[$pieId] = $pie;

    // Save to session
    $session_pies = new Zend_Session_Namespace('pies');
    $session_pies->pies = $this->_pies;
  }

  /**
   * Save the slices of a session stored pie to the DB.
   * ie. commit changes.
   * 
   * @param <type> $pieId
   * @return <type>
   */
  public function savePieSlices($pieId) {
    if ($pieId == 'new') return;
    
    $slices = $this->_getPieSlices($pieId);

    // This pie isn't loaded, therefore nothing to save
    if ($slices === null) return;

    // Delete existing
    $this->delete('pie_id = '.$pieId);

    // Create new slices
    foreach ($slices as $slice) {
      // Prepare slice for saving
      unset($slice['slice_id']);
      unset($slice['recipient_name']);
      $slice['pie_id'] = $pieId;
      // Save
      $this->insert($slice);
    }

    // Mark these pie slices as unchanged.
    $this->loadPieSlices($pieId);
  }

  /**
   * Move slices from one pie to another.
   * 
   * @param <type> $from
   * @param <type> $to
   */
  public function saveSlicesToPie($from,$to) {
    $slices = $this->getPieSlices($from);
    $this->_setPieSlices($to,$slices,$changed);
    $this->savePieSlices($to);
  }

  /**
   * Get slices of a pie from the DB, and store in the session.
   * 
   * @param <type> $pieId
   * @return <type>
   */
  public function loadPieSlices($pieId) {
    if ($pieId == 'new') return;
    
    $slices = $this->getAdapter()->fetchAll("
      SELECT s.slice_id, s.size, s.recipient_id, s.recipient_type, COALESCE(c.name, x.title) as recipient_name
        FROM pie_slices s
          LEFT OUTER JOIN charities c ON (s.recipient_id = c.charity_id AND s.recipient_type = 'CHARITY')
          LEFT OUTER JOIN causes x ON (s.recipient_id = x.cause_id AND s.recipient_type = 'CAUSE')
        WHERE s.pie_id = ".$pieId);
    $this->_setPieSlices($pieId,$slices,false);
    return $slices;
  }

  /**
   * Has a session stored pie been altered?
   * @param <type> $pieId
   * @return <type>
   */
  public function hasPieChanged($pieId) {
    $pie = $this->_pies[$pieId];
    return ($pie) ? $pie['changed'] : false;
  }

  /**
   * Returns all slices for a pie. If a session version exists then
   * this is returned.
   * 
   * @param <type> $pieId
   * @return <type>
   */
  public function getPieSlices($pieId)
  {
    $slices = $this->_getPieSlices($pieId);
    if ($slices === null && ($pieId != 'new')) {
      $slices = $this->loadPieSlices($pieId);
    }
    return ($slices === null) ? array() : $slices;
  }

  /**
   * Returns count of pie slices. If a session version exists then
   * this is used.
   * @param <type> $pieId
   * @return <type>
   */
  public function getPieSlicesCount($pieId)
  {
    $slices = $this->getPieSlices($pieId);
    return (count($slices));
  }
} 
