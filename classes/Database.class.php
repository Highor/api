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

	public function saveApiCall($data, Helper $helper) {
		$sth = $this->dbh->prepare("INSERT INTO api.calls (`app_id`, `url`, `file`, `type`) VALUES (:app_id, :url, :file, :type)");
		$sth->bindParam(':app_id', $data['app_id']);
		$sth->bindParam(':url', $data['apiurl']);
		$sth->bindParam(':file', $data['apifile']);
		$sth->bindParam(':type', $data['apitype']);
		$sth->execute();

		return $helper->addMessage('Succesfully added API call', 'success');
	}

	public function checkAPIUrl($data) {
		$sth = $this->dbh->prepare("SELECT id FROM api.calls WHERE `url` = :url");
		$sth->bindParam(':url', $data['apiurl']);
		$sth->execute();

		if ($sth->rowCount() == 0) {
			return true;
		}
		
		return false;
	}

	public function getApp($data) {
		$sth = $this->dbh->prepare("SELECT * FROM api.apps WHERE id = :id");
		$appID = $this->_getAppIDByName($data['REQUEST_URI']);
		$sth->bindParam(':id', $appID);
		$sth->execute();
		return $sth->fetch(PDO::FETCH_OBJ);
	}

	private function _getAppIDByName($name) {
		$sth = $this->dbh->prepare("SELECT * FROM api.apps WHERE name = :name");
		$appName = str_replace('/apps/', '', $name);
		$appName = str_replace('/', '', $appName);
		$sth->bindParam(':name', $appName);
		$sth->execute();
		$result = $sth->fetch(PDO::FETCH_OBJ);

		return $result->id;
	}

	public function getAppCalls($data) {
		$sth = $this->dbh->prepare("SELECT * FROM api.calls WHERE app_id = :app_id");
		$appID = $this->_getAppIDByName($data['REQUEST_URI']);
		$sth->bindParam(':app_id', $appID);
		$sth->execute();
		return $sth->fetchAll(PDO::FETCH_OBJ);

	}

	public function deleteApp($data) {
		$sth = $this->dbh->prepare("DELETE FROM api.apps WHERE id = :id");
		$sth->bindParam(':id', $data['deleteAppId']);
		$sth->execute();
	}

	public function getApps() {
		$sth = $this->dbh->prepare("SELECT * FROM api.apps");
		$sth->execute();
		return $sth->fetchAll(PDO::FETCH_OBJ);
	}

	public function saveApp($data) {
		$sth = $this->dbh->prepare("INSERT INTO api.apps (`name`, `basic_user`, `basic_key`) VALUES (:name, :basic_user, :basic_key)");
		$sth->bindParam(':name', $data['appname']);
		$sth->bindParam(':basic_user', $data['authuser']);
		$sth->bindParam(':basic_key', $data['authkey']);
		$sth->execute();
	}

	public function checkAppCredentionals($data) {
		$sth = $this->dbh->prepare("SELECT id FROM api.apps WHERE `name` = :name OR `basic_key` = :basic_key");
		$sth->bindParam(':name', $data['appname']);
		$sth->bindParam(':basic_key', $data['authkey']);
		$sth->execute();

		if ($sth->rowCount() == 0) {
			return true;
		}
		
		return false;
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