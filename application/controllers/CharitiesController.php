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

    if (isset($_REQUEST['listMethod']))
    {
      $listMethod = $_REQUEST['listMethod'];
      if ($listMethod == 'select')
      {
        $model = $this->_getModel();
        $categoryId = $_REQUEST['category_id'];
        $charities = $model->fetchCharitiesByCategory($categoryId);
        $category = $this->_getCategoryModel()->find($categoryId)->toArray();
        $title = "Browsing charities in the ".$category['label']." category";
      }
      if ($listMethod == 'search')
      {
        $str = $_REQUEST['s'];
        $charities = $model->fetchCharitiesBySearch($str);
        $title  = "Search results for '".$str."'";
      }
    }

    $this->view->charities = $charities;
    $this->view->list_title = $title;
  }

  public function viewAction() {
    $charity_id = $this->_getParam("charityid");

    $charity_model = $this->_getModel();

    # get info on the charity itself
    $charity = $charity_model->fetchCharityById($charity_id);
    $this->view->charity = $charity;
  }

}
