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
  protected $_donationModel;

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
  protected function _getDonationModel() {
		if (null == $this->_donationModel) {
			$this->_donationModel = new Model_DbTable_Donation();
		}
		return $this->_donationModel;
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
    $userId = $this->getUserId();

    $causes   = $this->getUserCauses();

    $donation = null;
    if ($userId > 0) {
      $donation = $this->_getDonationModel()->fetchCurrentUserDonation($userId);
    }

    $this->_helper->getHelper('Pie')->setViewPie($pieId,$this);

    $this->_helper->layout->setLayout('search_sidebar');
    $this->view->donate_form = $this->getDonateForm();
    $this->view->user       = $userId;
    $this->view->donation   = $donation;
    $this->view->pieId      = $pieId;
    $this->view->causes     = $causes;
    $this->view->cause_form = $this->getCreateCauseForm();
    $this->view->messages   = $this->_helper->FlashMessenger->getMessages();
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

  public function donateAction() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $donation = $_POST['donation'];
      $userId   = $this->getUserId();

      $this->_getDonationModel()->create($userId,$donation);

      $this->getHelper(redirector)->direct('index');
    }
    
    $this->view->donate_form = $this->getDonateForm();
  }

  public function getDonateForm() {
    $form = new Zend_Form();
    $form->setAction('mypie/donate');
    $form->addElement('text','donation');
    $form->addElement('submit','Donate');

    $form->removeDecorator('DtDdWrapper');
    $form->removeDecorator('Label');
    $form->removeDecorator('HtmlTag');
    foreach($form->getElements() as $element) {
      $element->removeDecorator('DtDdWrapper');
      $element->removeDecorator('Label');
      $element->removeDecorator('HtmlTag');
    }

    return $form;
  }

}
