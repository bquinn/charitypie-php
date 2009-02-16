<?php

class ProfileController extends Zend_Controller_Action
{

	public function init()
	{
		/* Initialize action controller here */
	}


	public function indexAction()
	{
		/* Default action for action controller */
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) {
			
			$username = $auth->getIdentity();
		}
	}
}
