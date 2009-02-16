<?php
/**
 * ProfileLink helper
 *
 * Call as $this->profileLink() in your layout script
 */
class My_View_Helper_ProfileLink extends Zend_View_Helper_Abstract
{
	public $view;
	protected $_userModel;

	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
	}

	public function profileLink()
	{
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) {
			$email = $auth->getIdentity();
			$userModel = $this->_getUserModel();
			$user = $userModel->fetchUserByEmail($email);
			$this->view->user = $user;
			// I feel like this stuff should really be
			// in a view template somewhere...
			return '<a href="/profile/' . $this->view->user['username'] . '/">Welcome, ' . $this->view->user['username'] .  '</a>' .
		' | <a href="/user/logout/">Logout</a>';
		} else {
			return '<a href="/user/register/">Register</a>'
				.' | '
				.'<a href="/user/login/">Login</a>';
		}
	}

	protected function _getUserModel() {
		if (null == $this->_userModel) {
			require_once APPLICATION_PATH . '/Model/User.php';
			$this->_userModel = new Model_User();
		}
		return $this->_userModel;
	}
}
