<?php
 /**
  * CharitiesController
  */
class CharitiesController extends Zend_Controller_Action {
  protected $_model;
  protected $_charityCategoryModel;

  public function indexAction() {
	$category_model = $this->_getCharityCategoryModel();
	$charity_categories = $category_model->fetchCategoryList();
	$this->view->charity_categories = $charity_categories;
  }

  public function viewAction() {
	$charity_id = $this->_getParam("charityid");

	$charity_model = $this->_getModel();
	$category_model = $this->_getCharityCategoryModel();

	# populate charity categories dropdown
	$charity_categories = $category_model->fetchCategoryList();
	$this->view->charity_categories = $charity_categories;

	# get info on the charity itself
	$charity = $charity_model->fetchCharityById($charity_id);
	$this->view->charity = $charity;
  }

  public function categoryAction() {
	$category_id = $this->_getParam("categoryid");

	$charity_model = $this->_getModel();
	$category_model = $this->_getCharityCategoryModel();

	# populate charity categories dropdown
	$charity_categories = $category_model->fetchCategoryList();
	$this->view->charity_categories = $charity_categories;

	# get info on this category for heading
	$category_info = $category_model->fetchById($category_id);
	$this->view->category_info = $category_info;

	# get info on the charity itself
	$charities = $charity_model->fetchCharitiesByCategory($category_id);
	$this->view->charities = $charities;
  }

  protected function _getModel() {
    if (null === $this->_model) {
      $this->_model = new Model_Charity();
    }
    return $this->_model;
  }

  protected function _getCharityCategoryModel() {
    if (null === $this->_charityCategoryModel) {
      $this->_charityCategoryModel = new Model_CharityCategory();
    }
    return $this->_charityCategoryModel;
  }

}
