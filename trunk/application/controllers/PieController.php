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

    $slices = $this->_getSliceModel()->getPieSlices($pieId);

    $total = 0;
    foreach ($slices as $slice) {
      $total += $slice['size'];
    }

    $i = 0;
    foreach ($slices as $slice) {
      $slices[$i]['percentage'] = ($slice['size']/$total)*100;
      $i++;
    }

    $this->view->pie = $pie;
    $this->view->slices = $slices;
  }

  public function saveAction() {
    $pieId = $this->_getParam('dataid');
    $slices = $this->_getSliceModel()->getPieSlices($pieId);
    foreach ($slices as $slice) {
      $size = $_POST['slice-'.$slice['slice_id']];
      $this->_getSliceModel()->updateSlice($pieId,$slice['slice_id'],$size);
    }
    $this->getHelper(redirector)->gotoRoute(array('controller'=>'mypie','action'=>'index'),'default');
  }
}