<?php

namespace Bixie\Framework\Routing;

class Response
{
	const DISPOSITION_ATTACHMENT = 'attachment';
	const DISPOSITION_INLINE = 'inline';

	/**
	 * @var string
	 */
	public $content;

	/**
	 * @var int
	 */
	public $status;

	/**
	 * @var  array
	 * @link http://www.iana.org/assignments/http-status-codes/
	 */
	public static $statuses = array(
		100 => 'Continue',
		101 => 'Switching Protocols',
		102 => 'Processing',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		207 => 'Multi-Status',
		208 => 'Already Reported',
		226 => 'IM Used',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		307 => 'Temporary Redirect',
		308 => 'Permanent Redirect',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',
		418 => 'I\'m a Teapot',
		422 => 'Unprocessable Entity',
		423 => 'Locked',
		424 => 'Failed Dependency',
		426 => 'Upgrade Required',
		428 => 'Precondition Required',
		429 => 'Too Many Requests',
		431 => 'Request Header Fields Too Large',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
		506 => 'Variant Also Negotiates',
		507 => 'Insufficient Storage',
		508 => 'Loop Detected',
		509 => 'Bandwidth Limit Exceeded',
		510 => 'Not Extended',
		511 => 'Network Authentication Required',
	);

	/**
	 * @var HeaderBag
	 */
	public $headers;

	public function __construct($content = '', $status = 200, array $headers = array())
	{

		foreach ($headers as $name => $value) {
			$this->setHeader($name, $value);
		}

		$this->setContent($content);
		$this->setStatus($status);

		$this->headers = new HeaderBag($headers);
	}

	public function prepare(Request $request)
	{
	}
	/**
	 * Returns a header value by name.
	 *
	 * @param  string $name
	 * @param  bool   $first
	 * @return mixed
	 */
	public function getHeader($name, $first = true)
	{
		$this->headers->get($name, null, $first);
	}

	/**
	 * Sets a header by name.
	 *
	 * @param  string $name
	 * @param  string $values
	 * @param  bool   $replace
	 * @return self
	 */
	public function setHeader($name, $values, $replace = true)
	{
		$this->headers->set($name, $values, $replace);
		return $this;
	}


	/**
	 * Returns the Last-Modified HTTP header as a DateTime instance.
	 *
	 * @return \DateTime|null A DateTime instance or null if the header does not exist
	 *
	 * @throws \RuntimeException When the HTTP header is not parseable
	 *
	 * @api
	 */
	public function getLastModified()
	{
		return $this->headers->getDate('Last-Modified');
	}

	/**
	 * Sets the Last-Modified HTTP header with a DateTime instance.
	 *
	 * Passing null as value will remove the header.
	 *
	 * @param \DateTime|null $date A \DateTime instance or null to remove the header
	 *
	 * @return Response
	 *
	 * @api
	 */
	public function setLastModified(\DateTime $date = null)
	{
		if (null === $date) {
			$this->headers->remove('Last-Modified');
		} else {
			$date = clone $date;
			$date->setTimezone(new \DateTimeZone('UTC'));
			$this->headers->set('Last-Modified', $date->format('D, d M Y H:i:s').' GMT');
		}

		return $this;
	}

	/**
	 * Returns the literal value of the ETag HTTP header.
	 *
	 * @return string|null The ETag HTTP header or null if it does not exist
	 *
	 * @api
	 */
	public function getEtag()
	{
		return $this->headers->get('ETag');
	}

	/**
	 * Sets the ETag value.
	 *
	 * @param string|null $etag The ETag unique identifier or null to remove the header
	 * @param bool        $weak Whether you want a weak ETag or not
	 *
	 * @return Response
	 *
	 * @api
	 */
	public function setEtag($etag = null, $weak = false)
	{
		if (null === $etag) {
			$this->headers->remove('Etag');
		} else {
			if (0 !== strpos($etag, '"')) {
				$etag = '"'.$etag.'"';
			}

			$this->headers->set('ETag', (true === $weak ? 'W/' : '').$etag);
		}

		return $this;
	}

	/**
	 * Generates a HTTP Content-Disposition field-value.
	 *
	 * @param string $disposition      One of "inline" or "attachment"
	 * @param string $filename         A unicode string
	 * @param string $filenameFallback A string containing only ASCII characters that
	 *                                 is semantically equivalent to $filename. If the filename is already ASCII,
	 *                                 it can be omitted, or just copied from $filename
	 *
	 * @return string A string suitable for use as a Content-Disposition field-value.
	 *
	 * @throws \InvalidArgumentException
	 *
	 * @see RFC 6266
	 */
	public function makeDisposition($disposition, $filename, $filenameFallback = '')
	{
		if (!in_array($disposition, array(self::DISPOSITION_ATTACHMENT, self::DISPOSITION_INLINE))) {
			throw new \InvalidArgumentException(sprintf('The disposition must be either "%s" or "%s".', self::DISPOSITION_ATTACHMENT, self::DISPOSITION_INLINE));
		}

		if ('' == $filenameFallback) {
			$filenameFallback = $filename;
		}

		// filenameFallback is not ASCII.
		if (!preg_match('/^[\x20-\x7e]*$/', $filenameFallback)) {
			throw new \InvalidArgumentException('The filename fallback must only contain ASCII characters.');
		}

		// percent characters aren't safe in fallback.
		if (false !== strpos($filenameFallback, '%')) {
			throw new \InvalidArgumentException('The filename fallback cannot contain the "%" character.');
		}

		// path separators aren't allowed in either.
		if (false !== strpos($filename, '/') || false !== strpos($filename, '\\') || false !== strpos($filenameFallback, '/') || false !== strpos($filenameFallback, '\\')) {
			throw new \InvalidArgumentException('The filename and the fallback cannot contain the "/" and "\\" characters.');
		}

		$output = sprintf('%s; filename="%s"', $disposition, str_replace('"', '\\"', $filenameFallback));

		if ($filename !== $filenameFallback) {
			$output .= sprintf("; filename*=utf-8''%s", rawurlencode($filename));
		}

		return $output;
	}

	/**
	 * Marks the response as "private".
	 *
	 * It makes the response ineligible for serving other clients.
	 *
	 * @return Response
	 */
	public function setPrivate()
	{
		$this->headers->removeCacheControlDirective('public');
		$this->headers->addCacheControlDirective('private');

		return $this;
	}

	/**
	 * Marks the response as "public".
	 *
	 * It makes the response eligible for serving other clients.
	 *
	 * @return Response
	 */
	public function setPublic()
	{
		$this->headers->addCacheControlDirective('public');
		$this->headers->removeCacheControlDirective('private');

		return $this;
	}

	/**
	 * Is response invalid?
	 *
	 * @return bool
	 *
	 * @api
	 */
	public function isInvalid()
	{
		return $this->status < 100 || $this->status >= 600;
	}

	/**
	 * Is response informative?
	 *
	 * @return bool
	 *
	 * @api
	 */
	public function isInformational()
	{
		return $this->status >= 100 && $this->status < 200;
	}

	/**
	 * Is response successful?
	 *
	 * @return bool
	 *
	 * @api
	 */
	public function isSuccessful()
	{
		return $this->status >= 200 && $this->status < 300;
	}

	/**
	 * Is the response a redirect?
	 *
	 * @return bool
	 *
	 * @api
	 */
	public function isRedirection()
	{
		return $this->status >= 300 && $this->status < 400;
	}

	/**
	 * Is there a client error?
	 *
	 * @return bool
	 *
	 * @api
	 */
	public function isClientError()
	{
		return $this->status >= 400 && $this->status < 500;
	}

	/**
	 * Was there a server side error?
	 *
	 * @return bool
	 *
	 * @api
	 */
	public function isServerError()
	{
		return $this->status >= 500 && $this->status < 600;
	}

	/**
	 * Is the response OK?
	 *
	 * @return bool
	 *
	 * @api
	 */
	public function isOk()
	{
		return 200 === $this->status;
	}

	/**
	 * Is the response forbidden?
	 *
	 * @return bool
	 *
	 * @api
	 */
	public function isForbidden()
	{
		return 403 === $this->status;
	}

	/**
	 * Is the response a not found error?
	 *
	 * @return bool
	 *
	 * @api
	 */
	public function isNotFound()
	{
		return 404 === $this->status;
	}

	/**
	 * Is the response a redirect of some form?
	 *
	 * @param string $location
	 *
	 * @return bool
	 *
	 * @api
	 */
	public function isRedirect($location = null)
	{
		return in_array($this->status, array(201, 301, 302, 303, 307, 308)) && (null === $location ?: $location == $this->headers->get('Location'));
	}

	/**
	 * Is the response empty?
	 *
	 * @return bool
	 *
	 * @api
	 */
	public function isEmpty()
	{
		return in_array($this->status, array(204, 304));
	}

	/**
	 * Gets the response content.
	 *
	 * @return string
	 */
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * Sets the response content.
	 *
	 * @param  mixed $content
	 * @return self
	 */
	public function setContent($content)
	{
		if ($content !== null && !is_string($content) && !is_numeric($content) && !is_callable(array($content, '__toString'))) {
			throw new \UnexpectedValueException(sprintf('The Response content must be a string or object implementing __toString(), "%s" given.', gettype($content)));
		}

		$this->content = (string) $content;

		return $this;
	}

	/**
	 * Gets the response status code.
	 *
	 * @return string
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * Sets the response status code.
	 *
	 * @param  string|int $status
	 * @return self
	 */
	public function setStatus($status = 200)
	{
		if (!isset(self::$statuses[$status])) {
			throw new \InvalidArgumentException(sprintf('The HTTP status code "%s" is not valid.', $status));
		}

		$this->status = $status;

		return $this;
	}

	/**
	 * Sends HTTP headers.
	 *
	 * @return Response
	 */
	public function sendHeaders()
	{
		if (headers_sent()) {
			return $this;
		}

		header(sprintf('HTTP/%s %s %s', 'HTTP/1.0' == $_SERVER['SERVER_PROTOCOL'] ? '1.0' : '1.1', $this->status, static::$statuses[$this->status]), true, $this->status);

		foreach ($this->headers as $name => $values) {
			foreach ($values as $value) {
				header("{$name}: {$value}", true, $this->status);
			}
		}

		return $this;
	}

	/**
	 * Sends content for the current web response.
	 *
	 * @return self
	 */
	public function sendContent()
	{
		echo $this->content;

		return $this;
	}

	/**
	 * Sends HTTP headers and content.
	 *
	 * @return self
	 */
	public function send()
	{
		$this->sendHeaders();
		$this->sendContent();

		if ('cli' !== PHP_SAPI) {
			flush();
		}

		return $this;
	}

	/**
	 * Returns the content as a string.
	 *
	 * @return  string
	 */
	public function __toString()
	{
		return (string) $this->content;
	}

}
