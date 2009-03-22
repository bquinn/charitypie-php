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

  protected function _redirect($pieId) {
    $myPieId = $this->_helper->Pie->getUserPieId();
    if ($pieId == $myPieId) {
      $this->getHelper(redirector)->gotoRoute(array('controller'=>'mypie','action'=>'index'),'default');
    } else {
      $cause = $this->_getPieModel()->fetchRow('pie_id = '.$pieId)->toArray();
      $this->getHelper(redirector)->gotoRoute(array('causeid'=>$cause['owner_id']),'viewcause');
    }
  }

  /**
   * Return all causes for currently authenticated user
   *
   * @return <type>
   */
  protected function getUserCauses() {
    $user = $this->_helper->Pie->getUserId();
    $causes = array();
    if ($user) {
      $causes = $this->_getCauseModel()->fetchCauses('user_id = '.$user);
    }
    return $causes;
  }

  public function loadAction() {
    $pieId = $this->_getParam('itemId');
    $pie = $this->_getPieModel('pie_id = '.$pieId);
    $this->_helper->layout->setLayout('blank');

    $slices = $this->_getSliceModel()->getPieSlices($pieId);

    $this->view->pie = $pie;
    $this->view->slices = $slices;
  }

  public function updateAction() {
    $pieId   = $this->_getParam('itemId');
    $myPieId = $this->_helper->Pie->getUserPieId();
    
    if (!$this->_helper->Pie->isPieOwner($pieId)) {
      die("UNAUTHORISED");
    }

    $slices = $this->_getSliceModel()->getPieSlices($pieId);
    foreach ($slices as $slice) {
      $size = $_POST['slice-'.$slice['slice_id']];
      $this->_getSliceModel()->updateSlice($pieId,$slice['slice_id'],$size);
    }

    $this->_redirect($pieId);
  }

 /**
   * addsliceAction()
   *
   * The slice to be added is determined from the url (addslice route).
   * If there is more than one possible pie to add to then a selection
   * form is presented to the user.
   *
   */
  public function addsliceAction() {
    $myPieId     = $this->_helper->Pie->getUserPieId();
    $type        = $this->_getParam('recipientType');
    $recipientId = $this->_getParam('recipientId');

    // Can't add a cause to a cause, so:
    if (($type == 'charity') && ($myPieId != 'new')) $causes = $this->getUserCauses();

    $pieId = $myPieId;

    if (!empty($causes)) {
      $selected = (isset($_POST['pie'])) ? $_POST['pie'] : null;
      if (!$selected) {
        // Collect array of available pies
        $pies = array();
        $pies[$pieId] = 'My Charity Pie';
        foreach ($causes as $cause) {
          // Can't add a cause to itself....
          if (!(($type == 'cause') && ($recipientId == $cause['cause_id']))) {
            $pies[$cause['pie_id']] = $cause['title'];
          }
        }
        // If more than 1 available allow user to select.
        if (count($pies) > 1) {
          $form = new Zend_Form;
          $form->setMethod('post');
          $form->setAction($this->getFrontController()->getBaseUrl().'/pie/add/'.$type.'/'.$recipientId);
          $form->addElement('select','pie',array('label'=>'Which pie?','multioptions'=>$pies));
          $form->addElement('submit','create');
          $this->view->form = $form;
          return;
        }
      } else { $pieId = $selected; }
    }

    if ($pieId) {
      if (!$this->_helper->Pie->isPieOwner($pieId)) {
        die("UNAUTHORISED");
      }
      $slices = $this->_getSliceModel()->getPieSlices($pieId);
      if (count($slices) < 10) {
        $this->_getSliceModel()->addSlice($pieId,$recipientId,strtoupper($type));
      } else {
        $flashMessenger = $this->_helper->FlashMessenger;
        $flashMessenger->addMessage("Sorry, we couldn't add another slice to this pie - it's reached the limit of 10 slices.");
      }
    }

    $this->_redirect($pieId);
  }
  
  public function deletesliceAction() {
    $sliceId = $this->_getParam('itemId');
    $pieId = $this->_getSliceModel()->getSlicePieId($sliceId);

    if ($this->_helper->Pie->isPieOwner($pieId)) {
      $this->_getSliceModel()->deleteSlice($sliceId);
    } else {
      print "UNAUTHORISED";
      exit;
    }

    $this->_redirect($pieId);
  }

  public function indexAction() {
    $pieId = $this->_getParam('itemId');
    $this->_helper->getHelper('Pie')->setViewPie($pieId,$this);
  }

  /**
   * Reload a session stored pie (ie. disgard changes)
   */
  public function reloadAction() {
    $pieId = $this->_getParam('itemId');
    $this->_getSliceModel()->loadPieSlices($pieId);
    $this->_redirect($pieId);
  }

  /**
   * Save a session stored pie to disk
   */
  public function saveAction() {
    $pieId = $this->_getParam('itemId');
    $this->_getSliceModel()->savePieSlices($pieId);
    $this->_redirect($pieId);
  }
}