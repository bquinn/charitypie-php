<?php

class Zend_Controller_Action_Helper_Pie extends Zend_Controller_Action_Helper_Abstract
{
  protected $_pieModel;
  protected $_sliceModel;
  protected $_causeModel;
  protected $_userModel;

  protected function _getPieModel() {
    if (null === $this->_pieModel) {
      $this->_pieModel = new Model_DbTable_Pie();
    }
    return $this->_pieModel;
  }
  protected function _getSliceModel() {
    if (null === $this->_sliceModel) {
      $this->_sliceModel = new Model_DbTable_Slice();
    }
    return $this->_sliceModel;
  }
	protected function _getUserModel() {
		if (null == $this->_userModel) {
			require_once APPLICATION_PATH . '/Model/User.php';
			$this->_userModel = new Model_User();
		}
		return $this->_userModel;
	}
  protected function _getCauseModel() {
		if (null == $this->_causeModel) {
			require_once APPLICATION_PATH . '/Model/DbTable/Cause.php';
			$this->_causeModel = new Model_DbTable_Cause();
		}
		return $this->_causeModel;
	}

  /**
   * Get the currently authenticated users id
   *
   * @return <int>
   */
  public function getUserId() {
		$userId = null;
    $auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) {
			$email = $auth->getIdentity();
			$userModel = $this->_getUserModel();
			$user = $userModel->fetchUserByEmail($email);
      $userId = $user['user_id'];
    }
    return $userId;
  }

  /**
   * Return the id of the user that owns $pieId
   *
   * @param <int> $pieId
   * @return <type>
   */
  public function getPieUser($pieId) {
    if ($pieId == 'new') return null;

    $pie = $this->_getPieModel()->fetchRow('pie_id = '.$pieId)->toArray();
    if ($pie['owner_type'] == 'CAUSE') {
      $cause = $this->_getCauseModel()
            ->fetchRow('cause_id = '.$pie['owner_id'])->toArray();
      $user = $cause['user_id'];
    } else {
      $user = $pie['owner_id'];
    }
    return $user;
  }

  /**
   * Is the currently authenticated user the owner of $pieId?
   *
   * @param <int> $pieId
   * @return <bool>
   */
  public function isPieOwner($pieId) {
    return ($this->getUserId() == $this->getPieUser($pieId));
  }

  /**
   * Returns the current authenticated users pie id
   */
  public function getUserPieId() {
		$pieId = null;
    $userId = $this->getUserId();
		if ($userId) {
      $pieId = $this->_getPieModel()->fetchPieId($userId,'USER');
    } else {
      $pieId = 'new';
    }
    return $pieId;
  }
  

  public function setViewPie($pieId,$controller) {
    $slices = null;
    $count = 0;
    
    if ($pieId) {
      $slices = $this->_getSliceModel()->getPieSlices($pieId);
      $count  = $this->_getSliceModel()->getPieSlicesCount($pieId);
    }

    $controller->view->is_pie_owner = $this->isPieOwner($pieId);
    $controller->view->pieId = $pieId;
    $controller->view->slices_count = $count;
    $controller->view->slice_changes = $this->_getSliceModel()->hasPieChanged($pieId);
    $controller->view->pie_slices = $slices;
  }
}