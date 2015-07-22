<?php

namespace GregorMorrill\ProcessWireShim;

/**
 * This is an extension of the WireHttp class to add support for HEAD
 * requests. It serves as a shim until ProcessWire core is updaed to
 * include it.
 * For more information: https://github.com/ryancramerdesign/ProcessWire/pull/928
 */

// error_reporting(E_ALL);
// ini_set('display_errors', TRUE);

class WireHttp extends \WireHttp
{

	/**
	 * Send to a URL using HEAD and return the status code (@horst)
	 *
	 * @param string $url URL to request (including http:// or https://)
	 * @param mixed $data Array of data to send (if not already set before) or raw data
	 * @param bool $textMode When true function will return a string rather than integer, see the statusText() method.
	 * @return bool|integer|string False on failure or integer of status code (200|404|etc) on success.
	 */
	 public function status($url, $data = array(), $textMode = FALSE)
	 {
	 	$response = $this->send($url, $data, 'HEAD');

	 	if ( $response === FALSE )
	 	{
	 		return $response;
	 	}

	 	$responseHeaders = $this->getResponseHeaders();

	 	$statusCode = 0;
	 	$statusText = '';

 		foreach ( $responseHeaders as $key => $value )
 		{

 			if ( preg_match("=^(HTTP/\d+\.\d+) (\d{3}) (.*)=", $key, $matches) === 1 )
 			{
 				$statusCode = intval($matches[2]);
 				$statusText = $matches[3];
 			}

 		}

	 	if ( $textMode && $statusText )
	 	{
	 		$statusCode = sprintf('%d %s', $statusCode, $statusText);
	 	}

	 	return $statusCode;
	}


	/**
	 * Send the given $data array to a URL using either POST or GET
	 *
	 * @param string $url URL to post to (including http:// or https://)
	 * @param array $data Array of data to send (if not already set before)
	 * @param string $method Method to use (either POST or GET)
	 * @return bool|string False on failure or string of contents received on success.
	 *
	 */
	protected function send($url, $data = array(), $method = 'POST')
	{
		$url = $this->validateURL($url, FALSE);

		if ( empty($url) )
		{
			return FALSE;
		}

		$this->resetResponse();
		$unmodifiedURL = $url;

		if ( !empty($data) )
		{
			$this->setData($data);
		}

		if ( !in_array($method, array('GET', 'POST', 'HEAD')) )
		{
			$method = 'POST';
		}

		if ( !$this->hasFopen || strpos($url, 'https://') === 0 && !extension_loaded('openssl') )
		{
			return $this->sendSocket($url, $method);
		}

		if ( !empty($this->data) )
		{
			$content = http_build_query($this->data);

			if ( $method === 'GET' && strlen($content) )
			{
				$url .= ( strpos($url, '?' ) === FALSE ? '?' : '&') . $content;
				$content = '';
			}

		}
		else if ( !empty($this->rawData) )
		{
			$content = $this->rawData;
		}
		else
		{
			$content = '';
		}

		$this->setHeader('content-length', strlen($content));

		$header = '';

		foreach ( $this->headers as $key => $value )
		{
			$header .= "$key: $value\r\n";
		}

		$options = array(
			'http' => array(
				'method' => $method,
				'content' => $content,
				'header' => $header
			)
		);

		$context = @stream_context_create($options);
		$fp = @fopen($url, 'rb', false, $context);

		if ( !$fp )
		{
			return $this->sendSocket($unmodifiedURL, $method);
		}

		$result = @stream_get_contents($fp);

		if ( isset($http_response_header) )
		{
			$this->setResponseHeader($http_response_header);
		}

		return $result;
	}


	/**
	 * Alternate method of sending when allow_url_fopen isn't allowed
	 */
	protected function sendSocket($url, $method = 'POST')
	{
		static $level = 0; // recursion level

		$this->resetResponse();
		$timeoutSeconds = 3;

		if ( !in_array($method, array('GET', 'POST', 'HEAD')) )
		{
			$method = 'POST';
		}

		$info = parse_url($url);
		$host = $info['host'];
		$path = empty($info['path']) ? '/' : $info['path'];
		$query = empty($info['query']) ? '' : '?' . $info['query'];

		if($info['scheme'] == 'https') {
			$port = 443;
			$scheme = 'ssl://';
		} else {
			$port = empty($info['port']) ? 80 : $info['port'];
			$scheme = '';
		}

		if(!empty($this->data)) {
			$content = http_build_query($this->data);
			if($method === 'GET' && strlen($content)) {
				$query .= (strpos($query, '?') === false ? '?' : '&') . $content;
				$content = '';
			}
		} else if(!empty($this->rawData)) {
			$content = $this->rawData;
		} else {
			$content = '';
		}

		$this->setHeader('content-length', strlen($content));

		$request = "$method $path$query HTTP/1.0\r\nHost: $host\r\n";

		foreach($this->headers as $key => $value) {
			$request .= "$key: $value\r\n";
		}

		$response = '';
		$errno = '';
		$errstr = '';

		if(false !== ($fs = fsockopen($scheme . $host, $port, $errno, $errstr, $timeoutSeconds))) {
			fwrite($fs, "$request\r\n$content");
			while(!feof($fs)) {
				// get 1 tcp-ip packet per iteration
				$response .= fgets($fs, 1160);
			}
			fclose($fs);
		}
		if(strlen($errstr)) $this->error = $errno . ': ' . $errstr;

		// skip past the headers in the response, so that it is consistent with
		// the results returned by the regular send() method
		$pos = strpos($response, "\r\n\r\n");
		$this->setResponseHeader(explode("\r\n", substr($response, 0, $pos)));
		$response = substr($response, $pos+4);

		// if response resulted in a redirect, follow it
		if($this->httpCode == 301 || $this->httpCode == 302) {
			// follow redirects
			$location = $this->getResponseHeader('location');
			if(!empty($location) && ++$level <= 5) {
				if(strpos($location, '://') === false && preg_match('{(https?://[^/]+)}i', $url, $matches)) {
					// if location is relative, convert to absolute
					$location = $matches[1] . '/' . ltrim($location, '/');
				}
				return $this->sendSocket($location, $method);
			}
		}

		return $response;
	}


	/**
	 * Set the response header
	 *
	 * @param array
	 *
	 */
	protected function setResponseHeader(array $responseHeader)
	{
		$this->responseHeader = $responseHeader;

		if ( isset($responseHeader[0]) )
		{
			$properties = explode(' ', $responseHeader[0]);
			$httpCode = isset($properties[1]) ? (int) $properties[1] : 0;
			$message = isset($properties[2]) ? $properties[2] : '';
		}
		else
		{
			$httpCode = 0;
			$message = '';
		}

		$this->httpCode = (int) $httpCode;

		if ( isset($this->errorCodes[$this->httpCode]) )
		{
			$this->error = $this->errorCodes[$this->httpCode];
		}

		// parsed version
		$this->responseHeaders = array();

		# loop: each response header
		foreach ( $responseHeader as $header )
		{

			# if:
			if ( strpos($header, ':') !== FALSE )
			{
				list($key, $value) = explode(':', $header, 2);
				$key = trim(strtolower($key));
				$value = trim($value);
			}
			else
			{
				$key = $header;
				$value = '';
			}

			if ( isset($this->responseHeaders[$key]) )
			{
				$this->responseHeaders[$key][] = $value;
			}
			else
			{
				$this->responseHeaders[$key] = array($value);
			}

		} # end loop: each response header

	} # end method setResponseHeader()

}
