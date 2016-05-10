<?php

namespace GregorMorrill\ProcessWireShim;

/**
 * This is an extension of the WireHttp class to add support for multiple
 * response headers of the same type. It serves as a shim until ProcessWire core is updaed to
 * include it.
 * For more information: https://github.com/ryancramerdesign/ProcessWire/pull/1704
 */

// error_reporting(E_ALL);
// ini_set('display_errors', TRUE);

class WireHttp extends \WireHttp
{

	/**
	 * Set the response header
	 *
	 * @param array
	 *
	 */
	protected function setResponseHeader(array $responseHeader) {

		$this->responseHeader = $responseHeader;

		if(!empty($responseHeader[0])) {
			list($http, $httpCode, $httpText) = explode(' ', trim($responseHeader[0]), 3);
			$httpCode = (int) $httpCode;
			$httpText = preg_replace('/[^-_.;() a-zA-Z0-9]/', ' ', $httpText);
		} else {
			$httpCode = 0;
			$httpText = '';
		}

		$this->httpCode = (int) $httpCode;
		$this->httpCodeText = $httpText;

		if(isset($this->errorCodes[$this->httpCode])) $this->error[] = $this->errorCodes[$this->httpCode];

		// parsed version
		$this->responseHeaders = array();
		foreach($responseHeader as $header) {
			$pos = strpos($header, ':');
			if($pos !== false) {
				$key = trim(strtolower(substr($header, 0, $pos)));
				$value = trim(substr($header, $pos+1));
			} else {
				$key = $header;
				$value = '';
			}
			if(!isset($this->responseHeaders[$key])) {
				$this->responseHeaders[$key] = $value;
			}
			else if(is_array($this->responseHeaders[$key])) {
				$this->responseHeaders[$key][] = $value;
			}
			else {
				$this->responseHeaders[$key] = array($this->responseHeaders[$key], $value);
			}
		}

		/*
		if(self::debug && count($responseHeader)) {
			$this->message("httpCode: $this->httpCode, message: $message");
			$this->message("<pre>" . print_r($this->getResponseHeader(true), true) . "</pre>", Notice::allowMarkup);
		}
		*/
	}

}
