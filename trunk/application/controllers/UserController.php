<?php
/* handles login, register, forgot password etc */
class UserController extends Zend_Controller_Action
{
	protected $_model;
  protected $_pieModel;
  protected $_sliceModel;

	protected function _getModel() {
		if (null == $this->_model) {
			$this->_model = new Model_User();
		}
		return $this->_model;
	}
  protected function _getPieModel() {
    if (null === $this->_pieModel) {
      $this->_pieModel = new Model_DbTable_Pie();
    }
    return $this->_pieModel;
  }
  protected function _getSliceModel() {
    if (null === $this->_sliceModel) {
      $this->_sliceModel = new Model_DbTable_Slice();
    }
    return $this->_sliceModel;
  }
	public function getLoginForm()
	{
		return new Forms_User_LoginForm(array(
			'action' => 'processlogin',
			'method' => 'post',
		));
	}

	public function getRegisterForm()
	{
		return new Forms_User_RegisterForm(array(
			'action' => 'processregister',
			'method' => 'post',
		));
	}

	public function getAuthAdapter(array $params)
	{
		$authAdapter = Zend_Registry::getInstance()->authAdapter;

		$authAdapter
			->SetIdentity($params["email"])
			->SetCredential($params["password"]);

		return $authAdapter;
	}

	public function preDispatch()
	{
		if (Zend_Auth::getInstance()->hasIdentity()) {
			// If the user is logged in, we don't want to show the login form;
			// however, the logout action should still be available
			if ('logout' != $this->getRequest()->getActionName()) {
				$this->_helper->redirector('index', 'index');
			}
		} else {
			// If they aren't, they can't logout, so that action should
			// redirect to the login form
			if ('logout' == $this->getRequest()->getActionName()) {
				$this->_helper->redirector('index');
			}
		}
	}

	public function loginAction()
	{
		$this->view->form = $this->getLoginForm();
	}

	/** handle a post on the login form */
	public function processloginAction()
	{
		$request = $this->getRequest();

		// Check if we have a POST request
		if (!$request->isPost()) {
			return $this->_helper->redirector('index');
		}

		// Get our form and validate it
		$form = $this->getLoginForm();
		if (!$form->isValid($request->getPost())) {
			// Invalid entries
			$this->view->form = $form;
			return $this->render('login'); // re-render the login form
		}

		// Get our authentication adapter and check credentials
		$adapter = $this->getAuthAdapter($form->getValues());
		$auth	= Zend_Auth::getInstance();
		$result  = $auth->authenticate($adapter);
		switch ($result->getCode()) {

			case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
				$form->setDescription('That username doesn\'t exist! Perhaps you need to register?');
				$this->view->form = $form;
				return $this->render('login'); // re-render the login form
				break; // should never get here

			case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
				$form->setDescription('Invalid credentials provided - bad password');
				$this->view->form = $form;
				return $this->render('login'); // re-render the login form
				break; // should never get here

			case Zend_Auth_Result::SUCCESS:
				// We're authenticated! Redirect to where the user
				// was before, or the home page if there was no referer
				// (the session will detect that we're logged in)
				if ($form->getValue('redirect_to')) {
					$this->_redirect($form->getValue('redirect_to'));
				} else {
					$this->_helper->redirector('index', 'mypie');
				}
				break; // should never get here

			default:
				break; // should never get here
		}
/* old way		if (!$result->isValid()) {
			// Invalid credentials
			$form->setDescription('Invalid credentials provided');
			$this->view->form = $form;
			return $this->render('login'); // re-render the login form
		}

		// We're authenticated! Redirect to the home page
		// (the session will detect that we're logged in)
		$this->_helper->redirector('index', 'index');
*/
	}

	/** handle a post on the register form */
	public function processregisterAction()
	{
		$request = $this->getRequest();
		$form = $this->getRegisterForm();

		// Check if we have a POST request
		if (!$request->isPost()) {
			return $this->_helper->redirector('index');
		}

		// Make sure the results are valid according to our form logic
		if (!$form->isValid($request->getPost())) {
			// Invalid entries
			$this->view->form = $form;
			return $this->render('register'); // re-render the register form (explaining errors)
		} else {
			// Valid form
			$model = $this->_getModel();
			$userId = $model->save($form->getValues());
			// save throws an exception if it doesn't work, so we can assume it did (I think)

      // AUTO CREATE A PIE
      $data = array(
        'owner_type'=>'USER',
        'owner_id'=>$userId
      );
      $pieId = $this->_getPieModel()->insert($data);

      // SAVE THE TEMPORARY PIE
      $this->_getSliceModel()->saveSlicesToPie('new',$pieId);
      
			// the auth adapter doesn't log in the new user automatically, so
			// we call it explicitly here (*almost* the same code as in
			// processloginAction above so we probably should abstract some
			// of this out)

			// as we've used the same field names we should be able to
			// authenticate against the same form we used to create the user
			$adapter = $this->getAuthAdapter($form->getValues());
			$auth	= Zend_Auth::getInstance();
			$result  = $auth->authenticate($adapter);
			switch ($result->getCode()) {

				case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
					throw Exception("We just created a user but now it's not found! Something's wrong...");
					break; // should never get here

				case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
					throw Exception("We just created a user but now the password is invalid! Something's wrong...");
					break; // should never get here

				case Zend_Auth_Result::SUCCESS:
					// We're authenticated! All is good
					break; // should never get here

				default:
					throw Exception("We just created a user but now we got a weird response from the authenticator! Something's wrong...");
					break; // should never get here
			}
			return $this->_helper->redirector('registersuccess', 'user');
		}
	}

	public function logoutAction()
	{
		Zend_Auth::getInstance()->clearIdentity();
		$this->_helper->redirector('index', 'index'); // back to home page
	}

	public function registerAction()
	{
		$this->view->form = $this->getRegisterForm();
	}
	public function registersuccessAction()
	{
	}
}
