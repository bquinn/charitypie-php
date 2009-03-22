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

  protected function _getModel() {
    if (null === $this->_model) {
      $this->_model = new Model_DbTable_Pie();
    }
    return $this->_model;
  }
  protected function _getCauseModel() {
		if (null == $this->_causeModel) {
			require_once APPLICATION_PATH . '/Model/DbTable/Cause.php';
			$this->_causeModel = new Model_DbTable_Cause();
		}
		return $this->_causeModel;
	}
  protected function _getUserModel() {
		if (null == $this->_userModel) {
			require_once APPLICATION_PATH . '/Model/User.php';
			$this->_userModel = new Model_User();
		}
		return $this->_userModel;
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

  public function indexAction() {
    $pieId = $this->_helper->Pie->getUserPieId();

    $causes = $this->getUserCauses();

    $this->view->pieId = $pieId;
    $this->view->causes = $causes;

    $this->_helper->getHelper('Pie')->setViewPie($pieId,$this);
    
    $this->view->cause_form = $this->getCreateCauseForm();
    $this->view->messages = $this->_helper->FlashMessenger->getMessages();
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
