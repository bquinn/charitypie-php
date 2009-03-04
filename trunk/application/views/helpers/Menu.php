<?php
/**
 * Menu helper
 *
 * Call as $this->menu("menuitem") in your layout script
 */
class My_View_Helper_Menu extends Zend_View_Helper_Abstract
{
	public $view;

	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
	}

	public function menu($active_menu_item_id)
	{
		## TODO: need to get this to use the URL view helper for the URLs
		$menu_items = array (
			# format: id, cells, URL, name
			'filler' => array (
				cells => 6, URL => null, name => null
			),
			'charities' => array (
				cells => 2, URL => $this->view->url(array('controller'=>'charities'),'default',true), name => 'Charities'
			),
			'causes' => array (
				cells => 2, URL => $this->view->url(array('controller'=>'causes'),'default',true), name => 'Causes'
			),
			'mypie' => array (
				cells => 2, URL => $this->view->url(array('controller'=>'mypie'),'default',true), name => 'My Pie'
			),
		);

		$output = "<ul id=\"nav\">\n";
		foreach ($menu_items as $id => $menu_item) {
			if ($menu_item{name} == null) {
				// name == null: don't render any text
				;
			} else {
				$output .= '<li id="nav-'.$id.'">';
//				if ($id == $active_menu_item_id) {
//					// id == passed in parameter: don't render a link
//					$output .= $menu_item{name};
//				} else {
//					// else render link and heading
					$output .= '<a href="'.$menu_item{URL}.'">'.$menu_item{name}.'</a>';
//				}
				$output .= "</li>\n";
			}
		}
		$output .= '</ul>'."\n";
		return $output;
	}
}
