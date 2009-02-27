<?php
 /**
  * CharitiesController
  *
  * Authors: nathan@yura.net
  * Date:    26/02/09
  */
class CharitiesController extends Zend_Controller_Action {
  protected $_model;
  protected $_charityCategoryModel;
  protected $_tagModel;

  public function indexAction() {
    $model = $this->_getModel();
    $charities = array();
    $title = '';

    if (isset($_REQUEST['listMethod']))
    {
      $listMethod = $_REQUEST['listMethod'];
      if ($listMethod == 'select')
      {
        $tagModel = $this->_getTagModel();
        $tagID = $_REQUEST['tag_id'];
        $charities = $model->fetchCharitiesByTag($tagID);
        $tag = $tagModel->fetchById($tagID);
        $title = "All charities tagged as '".$tag['label']."'";
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

  protected function _getModel() {
    if (null === $this->_model) {
      $this->_model = new Model_DbTable_Charity(); //Model_Charity();
    }
    return $this->_model;
  }

  protected function _getCharityCategoryModel() {
    if (null === $this->_charityCategoryModel) {
      $this->_charityCategoryModel = new Model_CharityCategory();
    }
    return $this->_charityCategoryModel;
  }

  protected function _getTagModel() {
    if (null === $this->_tagModel) {
      $this->_tagModel = new Model_Tag();
    }
    return $this->_tagModel;
  }

}
