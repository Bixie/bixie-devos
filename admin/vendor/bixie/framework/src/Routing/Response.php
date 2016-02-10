<?php

namespace Bixie\Framework\Routing;

use YOOtheme\Framework\Routing\HeaderBag;
use YOOtheme\Framework\Routing\Request;
use YOOtheme\Framework\Routing\Response as ResponseBase;

class Response extends ResponseBase
{
	const DISPOSITION_ATTACHMENT = 'attachment';
	const DISPOSITION_INLINE = 'inline';

	/**
	 * @var HeaderBag
	 */
	public $headers;

	public function __construct($content = '', $status = 200, array $headers = array())
	{

		parent::__construct($content, $status, $headers);

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


}
