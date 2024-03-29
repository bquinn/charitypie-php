<?php
 // APPLICATION CONSTANTS - Set the constants to use in this application.
 // These constants are accessible throughout the application, even in ini
 // files. We optionally set APPLICATION_PATH here in case our entry point
 // isn't index.php (e.g., if required from our test suite or a script).
 defined('APPLICATION_PATH')
   or define('APPLICATION_PATH', dirname(__FILE__));
   
 defined('APPLICATION_ENVIRONMENT')
    || define('APPLICATION_ENVIRONMENT',
        isset($_SERVER['APPLICATION_ENVIRONMENT'])
        ? $_SERVER['APPLICATION_ENVIRONMENT']
        : 'development');

 // CONFIGURATION - Setup the configuration object
 // The Zend_Config_Ini component will parse the ini file, and resolve all of
 // the values for the given section. Here we will be using the section name
 // that corresponds to the app's environment
 $configuration = new Zend_Config_Ini( APPLICATION_PATH . '/config/app.ini', APPLICATION_ENVIRONMENT );

 // COLORS

 define('COLORS',"#d01f3c,#356aa0,#C79810,#FF73B9,#8BFEA8,#CAFEB8,#6755E3,#8C8CFF,#7DFDD7,#FF8A8A");

 // SESSION - configure how we handle sessions
 // would prefer to join this up with the main config file but it doesn't seem to work?
 
 $sessionconfig = new Zend_Config_Ini( APPLICATION_PATH . '/config/session.ini', APPLICATION_ENVIRONMENT );
 if ($sessionconfig->toArray()) {
   Zend_Session::setOptions($sessionconfig->toArray());
 }
 Zend_Session::start();

 // DATABASE ADAPTER - Setup the database adapter
 // Zend_Db implements a factory interface that allows developers to pass in an
 // adapter name and some parameters that will create an appropriate database
 // adapter object. In this instance, we will be using the values found in the
 // "database" section of the configuration obj.
 $dbAdapter = Zend_Db::factory($configuration->database);

 // DATABASE TABLE SETUP - Setup the Database Table Adapter
 // Since our application will be utilizing the Zend_Db_Table component, we need
 // to give it a default adapter that all table objects will be able to utilize
 // when sending queries to the db.
 Zend_Db_Table_Abstract::setDefaultAdapter($dbAdapter); 

 // AUTH ADAPTER SETUP - Setup the authentication interface
 $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
 $authAdapter->setTableName($configuration->database->auth->table_name)
			 ->SetIdentityColumn($configuration->database->auth->identity_column)
			 ->setCredentialColumn($configuration->database->auth->credential_column);
 
 // REGISTRY - setup the application registry
 // An application registry allows the application to store application
 // necessary objects into a safe and consistent (non global) place for future
 // retrieval. This allows the application to ensure that regardless of what
 // happens in the global scope, the registry will contain the objects it
 // needs.
 $registry = Zend_Registry::getInstance();
 $registry->configuration = $configuration;
 $registry->dbAdapter = $dbAdapter; 
 $registry->authAdapter = $authAdapter; 

 // FRONT CONTROLLER - Get the front controller.
 // The Zend_Front_Controller class implements the Singleton pattern, which is a
 // design pattern used to ensure there is only one instance of
 // Zend_Front_Controller created on each request.
 $frontController = Zend_Controller_Front::getInstance();

 // CONTROLLER DIRECTORY SETUP - Point the front controller to your action
 // controller directory.
 $frontController->setControllerDirectory(APPLICATION_PATH . '/controllers');

 // CONTROLLER HELPERS
 Zend_Controller_Action_HelperBroker::addPath(APPLICATION_PATH.'/controllers/helpers');

 // APPLICATION ENVIRONMENT - Set the current environment.
 // Set a variable in the front controller indicating the current environment --
 // commonly one of development, staging, testing, production, but wholly
 // dependent on your organization's and/or site's needs.
 $frontController->setParam('env', APPLICATION_ENVIRONMENT);
 
 // LAYOUT SETUP - Setup the layout component
 // The Zend_Layout component implements a composite (or two-step-view) pattern
 // With this call we are telling the component where to find the layouts scripts.
 Zend_Layout::startMvc(APPLICATION_PATH . '/layouts/scripts');

 // VIEW SETUP - Initialize properties of the view object
 // The Zend_View component is used for rendering views. Here, we grab a "global"
 // view instance from the layout object, and specify the doctype we wish to
 // use. In this case, XHTML1 Strict.
 $view = Zend_Layout::getMvcInstance()->getView();
 $view->addHelperPath(APPLICATION_PATH . '/views/helpers', 'My_View_Helper');
 $view->doctype('XHTML1_STRICT');

 // ROUTER SETUP - translate from URLs to controllers and actions
 // using the values in the config file
 $router = $frontController->getRouter();
 $router->addConfig($configuration, 'routes');

 // CLEANUP - Remove items from global scope.
 // This will clear all our local boostrap variables from the global scope of
 // this script (and any scripts that called bootstrap). This will enforce
 // object retrieval through the application's registry. Globals are bad, mmkay?
 unset($frontController, $view, $configuration, $dbAdapter, $registry, $router); 
