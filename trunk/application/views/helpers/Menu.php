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
		$menu_items = array (
			# format: id, cells, URL, name
			'filler' => array (
				cells => 6, URL => null, name => null
			),
			'charities' => array (
				cells => 2, URL => '/charities/', name => 'Charities'
			),
			'causes' => array (
				cells => 2, URL => '/causes/', name => 'Causes'
			),
			'mypie' => array (
				cells => 2, URL => '/mypie/', name => 'My Pie'
			),
		);

		$output = '<div id="menu" class="container_12 menu-charities-filler">'."\n";

		foreach ($menu_items as $id => $menu_item) {
			$output .= '<div id="menu-'.$id.'" class="grid_'.$menu_item{cells}.'">';
			if ($menu_item{name} == null) {
				// name == null: don't render any text
				;
			} else if ($id == $active_menu_item_id) {
				// id == passed in parameter: don't render a link
				$output .= '<h6>'.$menu_item{name}.'</h6>';
			} else {
				// else render link and heading
				$output .= '<h6><a href="'.$menu_item{URL}.'">'.$menu_item{name}.'</a></h6>';
			}
			$output .= '</div>'."\n";
		}
		$output .= '<div class="clear">&nbsp;</div>'."\n";
		$output .= '</div>';
		return $output;
	}
}
