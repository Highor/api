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
	private $_service;

	function __construct(Helper $helper, View $view, Database $database, Service $service) {
		$this->_helper = $helper;
		$this->_view = $view;
		$this->_database = $database;
		$this->_service = $service;

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

								if (array_key_exists('apiurl', $_REQUEST)) {
									$data = $this->_helper->validateCreateAPICall($_REQUEST, $this->_database);
									if ($data === true) {
										$data = $this->_database->saveApiCall($_REQUEST, $this->_helper);
									}
								}

								$data['app'] = $this->_database->getApp($_SERVER);
								$data['calls'] = $this->_database->getAppCalls($_SERVER);
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
				// Validate API URL & Auth
				$this->_helper->validDBConnection($this->_database);
				$data = $this->_service->validateCall($this->_database);
				if ($data['tpl'] != '') {
					$this->_view->render('api/'.$data['tpl'], $this->_helper, $data, true, false);
				}
				
				
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
	new Database,
	new Service
);