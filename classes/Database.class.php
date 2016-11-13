<?php

/**
 *
 * @author     Alexander Linke <api@highor.com>
 * @copyright  2016 HighorBV
 *
 **/

class Database {

	var $dbh;

	public function try($data) {
		try {
			$this->dbh = new PDO('mysql:host='.$data['hostname'], $data['username'], $data['password']);
			return true;
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	public function initializeDB() {
		$sth = $this->dbh->prepare('SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = :dbname');
		$dbname = 'api';
		$sth->bindParam(':dbname', $dbname);
		$sth->execute();
		if ($sth->rowCount() == 0) {
			$sth = $this->dbh->prepare('CREATE DATABASE api');
			$sth->execute();
		}
	}

}