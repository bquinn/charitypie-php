<?php
 /**
  * Causes Controller
  *
  * Authors: nathan@yura.net
  * Date:    26/02/09
  */

class CausesController extends Zend_Controller_Action {
  protected $_model;
  protected $_pieModel;
  protected $_sliceModel;
  protected $pie;

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
	protected function _getUserModel() {
		if (null == $this->_userModel) {
			require_once APPLICATION_PATH . '/Model/User.php';
			$this->_userModel = new Model_User();
		}
		return $this->_userModel;
	}
  protected function _getUserId() {
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
   * Is the currently authenticated user the causes' owner?
   * @param <type> $causeId
   * @return <boolean>
   */
  protected function _isUserOwner($causeId) {
    $cause = $this->_getModel()->fetchRow($causeID)->toArray();
    $userId = $this->_getUserId();
    return ($causeId['user_id'] == $userId);
  }
  protected function _getEditForm($causeId) {
    $cause = $this->_getModel()->fetchRow('cause_id = '.$causeId)->toArray();
    $userId = $this->_getUserId();
    $form = null;
    if ($cause['user_id'] == $userId) {
      $form = new Zend_Form;
      $form->setMethod('post');
      $form->setAction('save');
      $form->addElement('text','name',array('value'=>$cause['title'],'label'=>'Title'));
      $form->addElement('textarea','desc',array('value'=>$cause['description'],'label'=>'Description'));
      $form->addElement('hidden','id',array('value'=>$causeId));
      $form->addElement('submit','Save');
    }
    return $form;
  }

  public function saveAction() {
    $causeId = $_POST['id'];
    $data = array(
      'title'=>$_POST['name'],
      'description'=>$_POST['desc']
    );
    $saved = $this->_getModel()->update($data,'cause_id = '.$causeId);
    $this->getHelper(redirector)->gotoRoute(array('causeid'=>$causeId),'viewcause');
  }

  /**
   * Reload a session stored pie (ie. disgard changes)
   */
  public function reloadpieAction() {
    $causeId = $this->_getParam('causeid');
    $pieId   = $this->_getPieModel()->fetchPieId($causeId,'CAUSE');
    $this->_getSliceModel()->loadPieSlices($pieId);
    $this->getHelper(redirector)->gotoRoute(array('causeid'=>$causeId),'viewcause',true);
  }

  /**
   * Save the session stored pie
   */
  public function savepieAction() {
    $causeId = $this->_getParam('causeid');
    $pieId   = $this->_getPieModel()->fetchPieId($causeId,'CAUSE');
    $this->_getSliceModel()->savePieSlices($pieId);
    $this->getHelper(redirector)->gotoRoute(array('causeid'=>$causeId),'viewcause',true);
  }

  public function indexAction() {
    $this->view->causes = $this->_getModel()->fetchAll()->toArray();
  }

  public function viewAction() {
    $causeId = $this->_getParam('causeid');
    $pieId   = $this->_getPieModel()->fetchPieId($causeId,'CAUSE');

    $model = $this->_getModel();
    $cause = $model->fetchRow('cause_id = '.$causeId)->toArray();

    $this->view->pieId     = $pieId;

    $this->_helper->getHelper('Pie')->setViewPie($pieId,$this);

    $this->view->edit_form = $this->_getEditForm($causeId);
    $this->view->cause     = $cause;
    $this->view->messages  = $this->_helper->FlashMessenger->getMessages();
  }
}
