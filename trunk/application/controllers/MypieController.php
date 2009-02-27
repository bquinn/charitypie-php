<?php
 /**
  * MyPieController
  *
  * Authors: nathan@yura.net
  * Date:    26/02/09
  */
class MyPieController extends Zend_Controller_Action {
  protected $_model;
  protected $_sliceModel;
  protected $_userModel;
  protected $_causeModel;

  protected function _getModel() {
    if (null === $this->_model) {
      $this->_model = new Model_DbTable_Pie();
    }
    return $this->_model;
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
  protected function getUserId() {
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
  protected function getPieUser($pieId) {
    if ($pieId == 'new') return null;
    
    $pie = $this->_getModel()->fetchRow('pie_id = '.$pieId)->toArray();
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
  protected function isPieOwner($pieId) {
    return ($this->getUserId() == $this->getPieUser($pieId));
  }

  /**
   * Returns the current authenticated users pie id
   */
  protected function getUserPieId() {
		$pieId = null;
    $userId = $this->getUserId();
		if ($userId) {
      $pieId = $this->_getModel()->fetchPieId($userId,'USER');
    } else {
      $pieId = 'new';
    }
    return $pieId;
  }

  /**
   * Return all causes for currently authenticated user
   *
   * @return <type>
   */
  public function getUserCauses() {
    $user = $this->getUserId();
    $causes = array();
    if ($user) {
      $causes = $this->_getCauseModel()->fetchCauses('user_id = '.$user);
    }
    return $causes;
  }

  /**
   * Reload a session stored pie (ie. disgard changes)
   */
  public function reloadpieAction() {
    $pieId = $this->getUserPieId();
    $this->_getSliceModel()->loadPieSlices($pieId);
    $this->getHelper(redirector)->gotoSimple('index');
  }

  /**
   * Save a session stored pie to disk
   */
  public function savepieAction() {
    $pieId   = $this->getUserPieId();
    $this->_getSliceModel()->savePieSlices($pieId);
    $this->getHelper(redirector)->gotoSimple('index');
  }

  public function indexAction() {
    $pieId = $this->getUserPieId();

    $slices = null;
    $count = 0;
    if ($pieId) {
      $slices = $this->_getSliceModel()->getPieSlices($pieId);
      $count  = $this->_getSliceModel()->getPieSlicesCount($pieId);
    }

    $causes = $this->getUserCauses();

    $this->view->pieId = $pieId;
    $this->view->causes = $causes;
    $this->view->slices_count = $count;
    $this->view->slice_changes = $this->_getSliceModel()->hasPieChanged($pieId);
    $this->view->pie_slices = $slices;
    $this->view->cause_form = $this->getCreateCauseForm();
    $this->view->messages = $this->_helper->FlashMessenger->getMessages();
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
    $myPieId     = $this->getUserPieId();
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
          $form->setAction($this->getFrontController()->getBaseUrl().'/mypie/add/'.$type.'/'.$recipientId);
          $form->addElement('select','pie',array('label'=>'Which pie?','multioptions'=>$pies));
          $form->addElement('submit','create');
          $this->view->form = $form;
          return;
        }
      } else { $pieId = $selected; }
    }

    if ($pieId) {
      if (!$this->isPieOwner($pieId)) {
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

    if ($pieId == $myPieId) {
      $this->getHelper(redirector)->direct('index');
    } else {
      $cause = $this->_getModel()->fetchRow('pie_id = '.$pieId)->toArray();
      $this->getHelper(redirector)->gotoRoute(array('causeid'=>$cause['owner_id']),'viewcause');
    }
  }

  public function deletesliceAction() {
    $sliceId = $this->_getParam('sliceid');
    $pieId = $this->_getSliceModel()->getSlicePieId($sliceId);
    
    if ($this->isPieOwner($pieId)) {
      $this->_getSliceModel()->deleteSlice($sliceId);
    } else {
      print "UNAUTHORISED";
      exit;
    }

    $pie = array();
    if ($pieId == 'new') {
      $pie['owner_type'] = 'USER';
    } else {
      $pie = $this->_getModel()->fetchRow('pie_id = '.$pieId)->toArray();
    }

    if ($pie['owner_type'] == 'USER') {
      $this->getHelper(redirector)->direct('index');
    } else {
      $this->getHelper(redirector)->gotoRoute(array('causeid'=>$pie['owner_id']),'viewcause');
    }
  }

  public function createcauseAction() {
    if (!$this->getRequest()->isPost()) {
      return $this->_forward('index');
    }

    $form = $this->getCreateCauseForm();

    if (!$form->isValid($_POST)) {
      $this->view->form = $form;
      return $this->render('form');
    }

    $userId = $this->getUserId();
    $posted = $form->getValues();

    if (!$userId) {
      return $this->getHelper(redirector)->direct('index');
    }

    $causeId = $this->_getCauseModel()->create($posted['name'],$userId);
    $pieId   = $this->_getModel()->createPie($causeId,'CAUSE');

    $this->getHelper(redirector)->direct('index');
  }

  public function getCreateCauseForm() {
    $form = new Zend_Form();
    $form->setAction('mypie/createcause');
    $form->addElement('text','name',array('label'=>'cause name'));
    $form->addElement('submit','create');
    return $form;
  }

}
