<?php
 /**
  * MyPieController
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
   * Returns the current authenticated users pie id
   */
  protected function getUserPieId() {
		$pieId = null;
    $userId = $this->getUserId();
		if ($userId) {
      $pieId = $this->_getModel()->fetchPieId($userId,'USER');
    }
    return $pieId;
  }
  
  public function indexAction() {
    $user = $this->getUserId();
    $pieId = $this->getUserPieId();

    $slices = array();
    $count = 0;
    if ($pieId) {
      $count = $this->_getSliceModel()->getPieSlicesCount($pieId);
      $slices = $this->_getSliceModel()->getPieSlices($pieId);
    }

    $causes = null;
    if ($user) {
      $causes = $this->_getCauseModel()->fetchAll('user_id = '.$user)->toArray();
    }

    $this->view->causes = $causes;
    $this->view->slices_count = $count;
    $this->view->pie_slices = $slices;
    $this->view->cause_form = $this->getCreateCauseForm();
  }

  public function addsliceAction() {
    $pieId = $this->getUserPieId();
    $type  = strtoupper($this->_getParam('recipientType'));
    $charityId = $this->_getParam('recipientId');
    if ($pieId) {
      $this->_getSliceModel()->addSlice($pieId,$charityId,$type);
    }
    $this->getHelper(redirector)->direct('index');
  }

  public function deletesliceAction() {
    $sliceId = $this->_getParam('sliceid');
    $this->_getSliceModel()->deleteSlice($sliceId);
    $this->getHelper(redirector)->direct('index');
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
