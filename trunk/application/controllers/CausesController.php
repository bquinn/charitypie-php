<?php
 /**
  * CausesController
  */
class CausesController extends Zend_Controller_Action {
  protected $_model;
  protected $_pieModel;
  protected $_sliceModel;

  protected function _getModel() {
		if (null == $this->_model) {
			require_once APPLICATION_PATH . '/Model/DbTable/Cause.php';
			$this->_model = new Model_DbTable_Cause();
		}
		return $this->_model;
	}
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

  public function indexAction() {
  }

  public function viewAction() {
    $causeId = $this->_getParam('causeid');
    $pieId   = $this->_getPieModel()->fetchPieId($causeId,'CAUSE');
    
    $cause = $this->_getModel()->fetchRow('cause_id = '.$causeId)->toArray();

    $slices = null;
    if ($pieId > 0) {
      $slices = $this->_getSliceModel()->getPieSlices($pieId);
    }

    $this->view->slices = $slices;
    $this->view->cause  = $cause;
  }
}
