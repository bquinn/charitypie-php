<?php
 /**
  * This is the login form. It is in its own directory in the application
  * structure because it represents a "composite asset" in your application. By
  * "composite", it is meant that the form encompasses several aspects of the
  * application: it handles part of the display logic (view), it also handles
  * validation and filtering (controller and model).
  */
class Forms_User_LoginForm extends Zend_Form {
 /**
  * init() is the initialization routine called when Zend_Form objects are
  * created. In most cases, it make alot of sense to put definitions in this
  * method, as you can see below. This is not required, but suggested.
  * There might exist other application scenarios where one might want to
  * configure their form objects in a different way, those are best
  * described in the manual:
  *
  * @see http://framework.zend.com/manual/en/zend.form.html
  * @return void
  */
  public function init() {

	// set the method for the display form to POST
	$this->setMethod('post');

/* let's check for email instead of username, for now
	$username = $this->addElement('text', 'username', array(
		'filters'	=> array('StringTrim', 'StringToLower'),
		'validators' => array(
			'Alpha',
			array('StringLength', false, array(3, 20)),
		),
		'required'   => true,
		'label'	  => 'Your username:',
	));
*/
	$email = $this->addElement('text', 'email', array(
		'filters'	=> array('StringTrim', 'StringToLower'),
		'validators' => array(
			'EmailAddress',
			array('StringLength', false, array(3, 80)),
		),
		'required'   => true,
		'label'	  => 'Your email address:',
	));

	$password = $this->addElement('password', 'password', array(
		'filters'	=> array('StringTrim'),
		'validators' => array(
			'Alnum',
			array('StringLength', false, array(6, 20)),
		),
		'required'   => true,
		'label'	  => 'Password:',
	));

	// we did have the captcha service in here, but that's a bit of overkill for a login form, right?
	// could maybe have something where on the third failed login attempt, we put a captcha up

	// include the original referer here, *if* the referer was
	// inside the *.mycharitypie.com/<something> domain.
	// this is so we can redirect back to the originating page after login
	// for user-interface niceness.
	$origreferer = $_SERVER['HTTP_REFERER'];
	preg_match("/http:\/\/(\w+)\.mycharitypie\.com\/.*/", $origreferer, $match);
	if ($match) {
		$redirect_to = $origreferer;
	}

	if ($redirect_to) {
		$redir = $this->addElement('hidden', 'redirect_to', array('value' => $redirect_to));
	}

	// login button
	$login = $this->addElement('button', 'login', array(
		'type' => 'submit',
		'required' => false,
		'ignore'   => true,
		'label'	=> 'Login',
	));

	// We want to display a 'failed authentication' message if necessary;
	// we'll do that with the form 'description', so we need to add that
	// decorator.
	$this->setDecorators(array(
		'FormElements',
		array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form')),
		array('Description', array('placement' => 'prepend')),
		'Form'
	));

  }
}
