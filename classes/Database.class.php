<?php

/**
 *
 * @author     Alexander Linke <api@highor.com>
 * @copyright  2016 HighorBV
 *
 **/

class Database {

	public function try($data) {
		try {
			$dbh = new PDO('mysql:host='.$data['hostname'].';dbname='.$data['dbname'], $data['username'], $data['password']);
			return true;
		} catch (PDOException $e) {
			return false;
		}
	}

}