<?php

/**
 *
 * @author     Alexander Linke <api@highor.com>
 * @copyright  2016 HighorBV
 *
 **/

class Service {

	public function validateCall(Database $database) {
		$data = array();
		$template = '';

		if ($database->checkAPIUrl($_SERVER, 'api') === false) {
			$data['code'] = 404;
			$data['response'] = 'API Url not found';
			$data['data'] = array();
			$template = '404';
		} else if ($database->validateBasicAuth($_SERVER) === false) {
			$data['code'] = 403;
			$data['response'] = 'Insufficient Authorization';
			$data['data'] = array();
			$template = '403';
		}

		return array('tpl' => $template, 'data' => $data);
	}

}