<?php
/**
 * Menu helper
 *
 * Call as $this->menu("menuitem") in your layout script
 */
class My_View_Helper_SearchSidebar extends Zend_View_Helper_Abstract
{
	public $view;

	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
	}

	public function SearchSidebar()
	{
    $model = new Model_DbTable_Charity();
    $this->view->charity_tags = $model->fetchAllCharityTags();
	}
}
