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

		switch ($_SERVER['REQUEST_URI']) {
			case '/apps':
				$this->_view->render('apps', $this->_helper);
			break;
			
			default:
				$this->_runApplication();
			break;
		}
	}

	private function _runApplication() {
		switch ($this->_helper->callType()) {
			case 'http':
				switch ($this->_helper->validDBConnection($this->_database)) {
					case true:
						$this->_database->initializeDB();
						$this->_helper->redirect('apps');
					break;
					
					default:
						if (array_key_exists('hostname', $_REQUEST) and array_key_exists('username', $_REQUEST) and array_key_exists('password', $_REQUEST)) {
							$response = $this->_database->try($_REQUEST);
							if ($response === true) {
								$this->_helper->saveDBConfig($_REQUEST);
								$this->_database->initializeDB();
								$this->_helper->redirect('apps');
							} else {
								$data = $this->_helper->addMessage($response, 'error');
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
    include 'classes/' . $class . '.class.php';
}

spl_autoload_register('my_autoloader');

new Api(
	new Helper,
	new View,
	new Database
);