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

new Api(new Helper, new View);

class Api {

	private $_helper;
	private $_view;

	function __construct($helper, $view) {
		$this->_helper = $helper;
		$this->_view = $view;

		$this->_runApplication();
	}

	private function _runApplication() {
		switch ($this->_helper->callType()) {
			case 'http':
				switch ($this->_helper->validDBConnection()) {
					case true:
						# show login page
						# or if logged in show other page
					break;
					
					default:
						if (array_key_exists('host', $_REQUEST) and array_key_exists('username', $_REQUEST) and array_key_exists('password', $_REQUEST)) {
							# Connect to database
							# if not error
							# if so save it in config file
							$data = $this->_helper->addMessage('Could not establish database connection.', 'error');
						} else {
							$data = $this->_helper->addMessage('No database connection found.', 'info');
						}
						
						# on submit check credentionals
						# OK: add db credentionals in config/database.ini & create database/tables
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