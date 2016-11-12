<?php

/**
 * API v1.0
 *
 * @author     Alexander Linke <api@highor.com>
 * @copyright  2016 HighorBV
 *
 **/

function my_autoloader($class) {
    include 'classes/' . $class . '.class.php';
}

spl_autoload_register('my_autoloader');

new Api(
	new Helper,
	new View,
	new Database
);

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
						# go to login page or if logged in show other page
					break;
					
					default:
						if (array_key_exists('hostname', $_REQUEST) and array_key_exists('username', $_REQUEST) and array_key_exists('password', $_REQUEST)) {
							if ($this->_database->try($_REQUEST)) {
								# if so save it in config file
								# go to login page or if logged in show other page
							} else {
								$data = $this->_helper->addMessage('Could not establish database connection.', 'error');
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