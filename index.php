<?php

/**
 * API v1.0
 *
 * @author     Alexander Linke <api@highor.com>
 * @copyright  2016 HighorBV
 *
 **/

class Api {

	private $_helper;
	private $_view;
	private $_database;

	function __construct($helper, $view, $database) {
		$this->_helper = $helper;
		$this->_view = $view;
		$this->_database = $database;

		$this->_runApplication();
	}

	private function _runApplication() {
		switch ($this->_helper->callType()) {
			case 'http':
				switch ($this->_helper->validDBConnection($this->_database)) {
					case true:
						$this->_database->initializeDB();
						switch ($_SERVER['REQUEST_URI']) {
							case (preg_match('^\/apps\/[a-zA-Z0-9]+\/^', $_SERVER['REQUEST_URI']) ? true : false):
								$this->_helper->isLoggedIn($this->_database);

								$data = array();
								$data['app'] = $this->_database->getApp($_SERVER);
								$this->_view->render('app', $this->_helper, $data);
							break;

							case '/logout':
								$this->_helper->logout();
							break;

							case '/apps':
								$this->_helper->isLoggedIn($this->_database);

								$data = array();
								if (array_key_exists('appname', $_REQUEST)) {
									$data = $this->_helper->validateCreateAppForm($_REQUEST, $this->_database);
									if ($data === true) {
										$this->_database->saveApp($_REQUEST);
										$this->_helper->redirect('apps/'.$_REQUEST['appname'].'/');
									}
								}

								if (array_key_exists('deleteAppId', $_REQUEST)) {
									$this->_database->deleteApp($_REQUEST);
								}

								$data['apps'] = $this->_database->getApps();
								$this->_view->render('apps', $this->_helper, $data);
							break;

							case '/login':
								$data = array();
								if (array_key_exists('adminusername', $_REQUEST)) {
									$data = $this->_helper->validateLogin($_REQUEST, $this->_database);
									if ($data === true) {
										$this->_helper->redirect('apps');
									}
								}

								$this->_view->render('login', $this->_helper, $data);
							break;

							default:
								$this->_helper->redirect('login');
							break;
						}
					break;
					
					default:
						if (array_key_exists('username', $_REQUEST)) {
							$data = $this->_helper->validateCreateForm($_REQUEST, $this->_database);
							if ($data === true) {
								$this->_helper->saveDBConfig($_REQUEST);
								$this->_database->initializeDB($_REQUEST);
								$this->_helper->redirect('login');
							}
						} else {
							$data = $this->_helper->addMessage('No database connection found.', 'info');
						}
						
						$this->_view->render('initialize_database', $this->_helper, $data);
					break;
				}
			break;
			
			default:
				# api call
			break;
		}
	}

}

error_reporting(E_ALL);
ini_set("display_errors", 1);

function my_autoloader($class) {
	$classFile = 'classes/' . $class . '.class.php';
	if (file_exists($classFile)) {
		include 'classes/' . $class . '.class.php';
	}
}

spl_autoload_register('my_autoloader');

new Api(
	new Helper,
	new View,
	new Database
);