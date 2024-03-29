<?php
 /**
  * CharitiesController
  *
  * Authors: nathan@yura.net
  * Date:    26/02/09
  */
class CharitiesController extends Zend_Controller_Action {
  protected $_model;
  protected $_categoryModel;
  protected $_tagModel;
  protected $_userModel;
  protected $_causeModel;

  protected function _getModel() {
    if (null === $this->_model) {
      $this->_model = new Model_DbTable_Charity(); //Model_Charity();
    }
    return $this->_model;
  }
  protected function _getCategoryModel() {
    if (null === $this->_categoryModel) {
      $this->_categoryModel = new Model_DbTable_Category();
    }
    return $this->_categoryModel;
  }
  protected function _getTagModel() {
    if (null === $this->_tagModel) {
      $this->_tagModel = new Model_Tag();
    }
    return $this->_tagModel;
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

  public function setupAction() {
    /**
     * Create categories
     */
    $cat = array();
    $cat[]['label'] = "Animals";
    $cat[]['label'] = "Aged";
    $cat[]['label'] = "Blind & Partialy Sighted";
    $cat[]['label'] = "Children & Youth";
    $cat[]['label'] = "Health";
    $cat[]['label'] = "Overseas Aid";

    $cm = $this->_getCategoryModel();

    $i = 0;
    foreach ($cat as $category) {
      $data = array('label'=>$category['label']);
      $existing = $cm->fetchRow("label='".$category['label']."'");
      
      if ($existing == null) {
        $category_id = $cm->insert($data);
      } else {
        $existing = $existing->toArray();
        $category_id = $existing['category_id'];
      }

      $cat[$i]['id'] = $category_id;

      $i++;
    }
    
    exit;
  }

  public function indexAction() {
    $model = $this->_getModel();
    $charities = array();
    $title = '';

    $listMethod = (isset($_REQUEST['listMethod']))?$_REQUEST['listMethod']:null;
    if ($listMethod == null) {
      // by url...
      $categoryId = $this->_getParam('categoryid');
      $search_str = $this->_getParam('search');
      if ($categoryId != null) $listMethod = 'browse';
      if ($search_str != null) $listMethod = 'search';
    } else {
      $categoryId = ($listMethod=='browse')?$_REQUEST['category_id']:0;
      $search_str = ($listMethod=='search')?$_REQUEST['s']:null;
    }

    if ($listMethod != null)
    {
      if ($listMethod == 'browse')
      {
        $select = $model->getCharitiesByCategorySelect($categoryId);
        $category = $this->_getCategoryModel()->find($categoryId)->toArray();
        $title = "Browsing charities in the ".$category[0]['label']." category";
        $page_url = array('categoryid'=>$categoryId);
        $page_route = 'category';
      }
      if ($listMethod == 'search')
      {
        $select = $model->getCharitiesBySearchSelect($search_str);
        $title  = "Search results for '".$search_str."'";
        $page_url = array('search'=>$search_str);
        $page_route = 'search';
      }
      /**
       * Pagination
       */
      $paginator = Zend_Paginator::factory($select);
      $page=$this->_getParam('page',1);
      $paginator->setItemCountPerPage(2);
      $paginator->setCurrentPageNumber($page);
    } else {
      $charities = $model->fetchMostPopularCharities();
      $this->view->charities = $charities;
    }

    $this->_helper->layout->setLayout('search_sidebar');
    $this->view->hasCauses  = $this->getUserCauses() ? 1 : 0;
    $this->view->paginator  = $paginator;
    $this->view->page_url   = $page_url;
    $this->view->page_route = $page_route;
    $this->view->list_title = $title;
  }

  public function viewAction() {
    
    $charity_id = $this->_getParam("charityid");

    $charity_model = $this->_getModel();

    # get info on the charity itself
    $charity = $charity_model->fetchCharityDetails($charity_id);

    if ($this->getRequest()->isXmlHttpRequest() == 1) {
      $this->_helper->layout->setLayout('blank');
    } else {
      $this->_helper->layout->setLayout('search_sidebar');
    }
    $this->view->charity = $charity;
    $this->view->hasCauses  = $this->getUserCauses() ? 1 : 0;
  }

}
