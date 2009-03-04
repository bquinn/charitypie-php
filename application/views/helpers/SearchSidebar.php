<?php
/**
 * Menu helper
 *
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
    $charity_tags = $model->fetchAllCharityTags();

    $action_url = $this->view->url(array('controller'=>'charities'),'default',true);
    ?>
    <div id="sidebar">        <h3>Search</h3>
        <form method="post" action="<?php print $action_url ?>">
            <input type="hidden" name="listMethod" value="search" />
            <input type="text" id="search" name="s" value="Enter keyword to search" onfocus="javascript:if (this.value=='Enter keyword to search') this.value='';" onblur="javascript:if (this.value=='') this.value='Enter keyword to search';" />
            <input type="submit" value="Search" />
        </form>
        <h3>Categories</h3>
        <form method="post" action="<?php print $action_url ?>">
            <input type="hidden" name="listMethod" value="select" />
            <select name="tag_id" id="category">            <?php
                foreach ($charity_tags as $tag) {
                    print('<option value="'.$tag['tag_id'].'">'.$tag['label'].' ('.$tag['charities'].')</option>');
                }
            ?>
            </select>
            <input type="submit" value="Search" />
        </form>
    </div>
    <?php
	}
}
