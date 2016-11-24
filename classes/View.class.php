<?php

/**
 *
 * @author     Alexander Linke <api@highor.com>
 * @copyright  2016 HighorBV
 *
 **/

 class View {

 	CONST PATH = 'views/';
 	CONST EXT = '.phtml';
 	CONST HEADER = 'header';
 	CONST VIEW = '404';
 	CONST FOOTER = 'footer';

 	public function render($view, Helper $helper, $data = array(), $api = false, $header = false, $footer = false) {
 		if ($api !== false) {
 			$headerPath = $this->_setHeader($header);
 		}
 		$viewPath = $this->_setView($view);
 		if ($api !== false) {
	 		$footerPath = $this->_setFooter($footer);
	 	}

 		if (strpos($viewPath, '404') !== false) {
 			header("HTTP/1.0 404 Not Found");
 		}

		ob_start();
		if ($api !== false) {
			include($headerPath);
		}
		include($viewPath);
		if ($api !== false) {
			include($footerPath);
		}
		$content = ob_get_contents();
		ob_end_clean();
		echo $content;
 	}

 	private function _setHeader($header) {
 		$headerPath = self::PATH . self::HEADER . self::EXT;
 		if ($header !== false) {
 			$headerPathTmp = self::PATH . $header . self::EXT;
 			if (file_exists($headerPathTmp)) {
 				$headerPath = $headerPathTmp;
 			}
 		}
 		return $headerPath;
 	}

 	private function _setView($view) {
 		$viewPath = self::PATH . self::VIEW . self::EXT;
 		if ($view !== NULL) {
 			$viewPathTmp = self::PATH . $view . self::EXT;
 			if (file_exists($viewPathTmp)) {
 				$viewPath = $viewPathTmp;
 			}
 		}
 		return $viewPath;
 	}

 	private function _setFooter($footer) {
 		$footerPath = self::PATH . self::FOOTER . self::EXT;
 		if ($footer !== false) {
 			$footerPathTmp = self::PATH . $footer . self::EXT;
 			if (file_exists($footerPathTmp)) {
 				$footerPath = $footerPathTmp;
 			}
 			
 		}
 		return $footerPath;
 	}

 }