<?php

class Model_DbTable_Donation extends Zend_Db_Table_Abstract {
  protected $_name = "donations";

  public function fetchCurrentUserDonation($userId) {
    $select = new Zend_Db_Table_Select($this);
    $select->where('user_id = ?',$userId);
    $select->order('date ASC');
    $donation = $this->fetchRow($select);
    if ($donation) {
      $donation = $donation->toArray();
    } else {
      $donation = null;
    }
    return $donation;
  }

  public function create($userId,$donation) {
    $data = array(
      'user_id'=>$userId,
      'amount'=>$donation,
      'date'=>date('Y-m-d h:m:s')
    );
    $this->insert($data);
  }
}