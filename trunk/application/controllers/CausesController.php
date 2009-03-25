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
  protected $_tagModel;
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
  protected function _getTagModel() {
		if (null == $this->_tagModel) {
			//require_once Model_DbTable_Tag();
			$this->_tagModel = new Model_DbTable_Tag();
		}
		return $this->_tagModel;
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

  protected function _getTaggingForm($causeId) {
    $form = new Zend_Form();
    $form->setMethod('post');
    $form->setAction('tag');
    $form->addElement('text','label',array('value'=>$cause['title']));
    $form->addElement('hidden','causeid',array('value'=>$causeId));
    $form->addElement('submit','tag');

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


  public function createAction() {
    if (!$this->getRequest()->isPost()) {
      return $this->_forward('index');
    }

    $form = $this->getCreateCauseForm();

    if (!$form->isValid($_POST)) {
      $this->view->form = $form;
      return $this->render('form');
    }

    $userId = $this->_getUserId();
    $posted = $form->getValues();

    if (!$userId) {
      return $this->getHelper(redirector)->direct('index');
    }

    $causeId = $this->_getModel()->create($posted['name'],$userId);
    $pieId   = $this->_getPieModel()->createPie($causeId,'CAUSE');

    $this->getHelper(redirector)->direct('index');
  }

  public function getCreateCauseForm() {
    $form = new Zend_Form();
    $form->setAction('causes/create');
    $form->addElement('text','name',array('label'=>'cause name'));
    $form->addElement('submit','create');
    return $form;
  }


  protected function getUserCauses() {
    $user = $this->_getUserId();
    $causes = array();
    if ($user) {
      $causes = $this->_getModel()->fetchCauses('user_id = '.$user);
    }
    return $causes;
  }

  public function tagAction() {
    $causeId = $_POST['causeid'];
    $tag     = $_POST['label'];
    $userId  = $this->_getUserId();

    $this->_getModel()->tagCause($tag,$causeId,$userId);

    $this->_helper->redirector->gotoRoute(array('causeid'=>$causeId),'viewcause');
  }

  public function removetagAction() {
    $tagId   = $this->_getParam('tagid');
    $causeId = $this->_getParam('causeid');
    $userId  = $this->_getUserId();

    $this->_getModel()->removeTag($tagId,$causeId,$userId);

    $this->getHelper(redirector)->gotoRoute(array('causeid'=>$causeId),'viewcause');
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
    $causes   = $this->getUserCauses();

    $this->_helper->layout->setLayout('search_sidebar');
    $this->view->cause_form = $this->getCreateCauseForm();
    $this->view->causes = $causes;
    $this->view->tags = $this->_getModel()->fetchAllTags();
  }

  public function browseAction() {
    $tagId = $this->_getParam('tagid');

    $tag = $this->_getTagModel()->find($tagId)->toArray();

    $select = $this->_getModel()->getCausesByTagSelect($tagId);

    $paginator = Zend_Paginator::factory($select);
    $page=$this->_getParam('page',1);
    $paginator->setItemCountPerPage(4);
    $paginator->setCurrentPageNumber($page);

    $page_url = array('tagid'=>$tagId);

    $this->_helper->layout->setLayout('search_sidebar');
    $this->view->page_route = "browsecauses";
    $this->view->page_url = $page_url;
    $this->view->tag = $tag[0];
    $this->view->paginator  = $paginator;
  }

  public function viewAction() {
    $causeId = $this->_getParam('causeid');
    $pieId   = $this->_getPieModel()->fetchPieId($causeId,'CAUSE');

    $model = $this->_getModel();
    $cause = $model->fetchCauseDetails($causeId);

    $tags = $this->_getModel()->fetchCauseTags($causeId);

    $user_tags = null;
    $userId = $this->_getUserId();
    if ($userId > 0) $user_tags = $this->_getModel()->fetchUserCauseTags($causeId,$userId);

    $this->view->pieId     = $pieId;

    $tagging_count = $this->_getModel()->fetchTaggingUserCount($causeId);

    $this->_helper->getHelper('Pie')->setViewPie($pieId,$this);

    if ($this->getRequest()->isXmlHttpRequest() == 1) {
      $this->_helper->layout->setLayout('blank');
    } else {
      $this->_helper->layout->setLayout('search_sidebar');
    }
    $this->view->tagging_count = $tagging_count;
    $this->view->user      = $userId;
    $this->view->user_tags = $user_tags;
    $this->view->tagging_form = $this->_getTaggingForm($causeId);
    $this->view->all_tags  = $tags;
    $this->view->edit_form = $this->_getEditForm($causeId);
    $this->view->cause     = $cause;
    $this->view->messages  = $this->_helper->FlashMessenger->getMessages();
  }
}
