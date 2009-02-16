<?php
 /**
  * This is the registration form.
  */
class Forms_User_RegisterForm extends Zend_Form {
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

	$name = $this->addElement('text', 'name', array(
		'filters'	=> array('StringTrim', 'StringToLower'),
		'validators' => array(
			array('Alnum', false, array('AllowWhiteSpace'=>true)),
			array('StringLength', false, array(3, 20)),
		),
		'required'   => false,
		'label'	  => 'Your full name',
	));

	$username = $this->addElement('text', 'username', array(
		'filters'	=> array('StringTrim', 'StringToLower'),
		'validators' => array(
			'Alnum',
			array('StringLength', false, array(3, 20)),
		),
		'required'   => true,
		'label'	  => 'Choose a username',
	));

	$email = $this->addElement('text', 'email', array(
		'filters'	=> array('StringTrim', 'StringToLower'),
		'validators' => array(
			'EmailAddress',
			array('StringLength', false, array(3, 80)),
		),
		'required'   => true,
		'label'	  => 'Your email address',
	));

	$password = $this->addElement('password', 'password', array(
		'filters'	=> array('StringTrim'),
		'validators' => array(
			'Alnum',
			array('StringLength', false, array(6, 20)),
		),
		'required'   => true,
		'label'	  => 'Choose a password',
	));

	// use the ReCaptcha service, which is quite cool and very easy to do in ZF!
//	$this->addElement('captcha', 'captcha', array(
	$captcha = new Zend_Form_Element_Captcha('captcha', array(
		'label' => 'Please enter the two words displayed.',
		'required' => true,
		'captcha' => 'ReCaptcha',
		'captchaOptions' => array(
			'pubkey' => '6LdFIwUAAAAAANW77vbGRJUigzd537AT4lMrUN1M',
			'privkey' => '6LdFIwUAAAAAAGf7sI2GYKftw5QtZEeB0onddKC4'
		),
		'description' => 'If you can\'t read the words, press the refresh button in the ReCaptcha box to get a new pair of words. Thanks for helping universities to digitize books!',
	));
	$captcha->addDecorator('HtmlTag', array('tag' => 'dd', 'class' => 'recaptcha'));
	$this->addElement($captcha);

	// register button
	$register = $this->addElement('button', 'register', array(
		'type' => 'submit',
		'required' => false,
		'ignore'   => true,
		'label'	=> 'Register',
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
