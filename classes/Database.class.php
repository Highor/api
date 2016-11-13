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
						$sql = str_replace("{password}", base64_encode(password_hash($data['adminpassword'], PASSWORD_BCRYPT)), $sql);
					break;
				}
				
				$sth = $this->dbh->prepare($sql);
				$sth->execute();
			}
		}
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