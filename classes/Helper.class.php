<?php

/**
 *
 * @author     Alexander Linke <api@highor.com>
 * @copyright  2016 HighorBV
 *
 **/

class Helper {

	public function callType() {
		return (1 == 1 ? 'http' : 'api');
	}

	public function validDBConnection() {
		$dbData = parse_ini_file('../config/database.ini');

		if (!array_key_exists('username', $dbData) OR trim($dbData['username']) == ''
			OR !array_key_exists('password', $dbData) OR trim($dbData['password']) == ''
			OR !array_key_exists('dbname', $dbData) OR trim($dbData['dbname']) == '') {
			return false;
		}

		// check connection and database!
		// XXX TODO
		return true;
	}

}