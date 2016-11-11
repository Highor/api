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

new Api(new Helper);

class Api {

	private $_helper;

	function __construct($helper) {
		$this->_helper = $helper;

		switch ($this->_helper->callType()) {
			case 'http':
				switch ($this->_helper->validDBConnection()) {
					case true:
						# show login page
						# or if logged in show other page
					break;
					
					default:
						# on submit check credentionals
						# OK: add db credentionals in config/database.ini & create database/tables
						var_dump("create database view");
					break;
				}
			break;
			
			default:
				# api call
			break;
		}
	}

}