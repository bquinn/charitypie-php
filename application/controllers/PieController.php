<?php
 /**
  * Pie Controller
  *
  * Authors: nathan@yura.net
  * Date:    26/02/09
  */

class PieController extends Zend_Controller_Action {

  protected $_pieModel;
  protected $_sliceModel;

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

  public function loadAction() {
    $pieId = $this->_getParam('dataid');
    $pie = $this->_getPieModel('pie_id = '.$pieId);
    $this->_helper->layout->setLayout('blank');
    $this->view->pie = $pie;
    $this->view->slices = $this->_getSliceModel()->getPieSlices($pieId);
  }
}