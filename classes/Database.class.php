<?php

/**
 *
 * @author     Alexander Linke <api@highor.com>
 * @copyright  2016 HighorBV
 *
 **/

class Database {

	CONST PATCHDIR = 'patches/sql/';
	var $dbh;

	public function try($data) {
		try {
			$this->dbh = new PDO('mysql:host='.$data['hostname'], $data['username'], $data['password']);
			return true;
		} catch (PDOException $e) {
			return $e->getMessage();
		}
	}

	public function checkCredentionals($data) {
		$sth = $this->dbh->prepare("SELECT id FROM api.login WHERE id = :id AND hash = :hash");
		$sth->bindParam(':id', $data['user_id']);
		$sth->bindParam(':hash', $data['hash']);
		$sth->execute();
		if ($sth->rowCount() == 0) {
			return false;
		}
		return true;
	}

	public function login($data) {
		$password = $this->_generatePassword($data['adminpassword']);
		$sth = $this->dbh->prepare("SELECT id, password FROM api.login WHERE username = :username");
		$sth->bindParam(':username', $data['adminusername']);
		$sth->execute();
		if ($sth->rowCount() == 0) {
			return false;
		}
		$result = $sth->fetch(PDO::FETCH_OBJ);

		if (password_verify($data['adminpassword'], base64_decode($result->password)) === false) {
			return false;
		}

		$user_id = $result->id;

		$hash = base64_encode(substr($password, 0, 5) . time() . rand());

		$sth = $this->dbh->prepare("UPDATE api.login SET hash = :hash WHERE username = :username");
		$sth->bindParam(':hash', $hash);
		$sth->bindParam(':username', $data['adminusername']);
		$sth->execute();

		session_start();
		$_SESSION['login']['user_id'] = $user_id;
		$_SESSION['login']['hash'] = $hash;

		return true;
	}

	public function initializeDB($data = array()) {
		$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(self::PATCHDIR),RecursiveIteratorIterator::SELF_FIRST);
		$files = iterator_to_array($it);
		$files = array_map(function($file) { return (string) $file; }, $files);
		unset($files['patches/sql/.']);
		unset($files['patches/sql/..']);
		natsort($files);

		foreach ($files as $file) {
			$runPath = $this->_runPath($file);
			if ($runPath === true) {
				$sql = file_get_contents($file);

				switch ($file) {
					case 'patches/sql/v1__createDatabase.sql':
						$sql = str_replace("{username}", $data['adminusername'], $sql);
						$sql = str_replace("{password}", $this->_generatePassword($data['adminpassword']), $sql);
					break;
				}
				
				$sth = $this->dbh->prepare($sql);
				$sth->execute();
			}
		}
	}

	private function _generatePassword($password) {
		return base64_encode(password_hash($password, PASSWORD_BCRYPT));
	}

	private function _runPath($file) {
		$sth = $this->dbh->prepare("SELECT id FROM api.patches WHERE name = :name");
		$sth->bindParam(':name', $file);
		$sth->execute();
		if ($sth->rowCount() == 0) {
			return true;
		}
		return false;
	}

}