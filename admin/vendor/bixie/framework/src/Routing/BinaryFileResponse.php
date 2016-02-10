<?php

namespace Bixie\Framework\Routing;


use Bixie\Framework\File\Exception\FileException;
use Bixie\Framework\File\File;
use YOOtheme\Framework\Routing\Request;

class BinaryFileResponse extends Response
{
	protected static $trustXSendfileTypeHeader = false;

	/**
	 * @var File $file
	 */
	protected $file;
	/**
	 * @var int
	 */
	protected $offset;
	/**
	 * @var int
	 */
	protected $maxlen;

	/**
	 * Constructor.
	 * @param mixed $file
	 * @param int   $status
	 * @param array $headers
	 * @param bool  $public
	 * @param null  $contentDisposition
	 * @param bool  $autoEtag
	 * @param bool  $autoLastModified
	 */
	public function __construct($file = null, $status = 200, $headers = array(), $public = true, $contentDisposition = null, $autoEtag = false, $autoLastModified = true)
	{
		parent::__construct(null, $status, $headers);

		$this->setFile($file, $contentDisposition, $autoEtag, $autoLastModified);

		if ($public) {
			$this->setPublic();
		}
	}


	/**
	 * Sets the file to stream.
	 *
	 * @param \SplFileInfo|string $file               The file to stream
	 * @param string              $contentDisposition
	 * @param bool                $autoEtag
	 * @param bool                $autoLastModified
	 *
	 * @return BinaryFileResponse
	 *
	 * @throws FileException
	 */
	public function setFile($file, $contentDisposition = null, $autoEtag = false, $autoLastModified = true)
	{
		if (!$file instanceof File) {
			if ($file instanceof \SplFileInfo) {
				$file = new File($file->getPathname());
			} else {
				$file = new File((string) $file);
			}
		}

		if (!$file->isReadable()) {
			throw new FileException('File must be readable.');
		}

		$this->file = $file;

		if ($autoEtag) {
			$this->setAutoEtag();
		}

		if ($autoLastModified) {
			$this->setAutoLastModified();
		}

		if ($contentDisposition) {
			$this->setContentDisposition($contentDisposition);
		}

		return $this;
	}

	/**
	 * Gets the file.
	 *
	 * @return File The file to stream
	 */
	public function getFile()
	{
		return $this->file;
	}

	/**
	 * Automatically sets the Last-Modified header according the file modification date.
	 */
	public function setAutoLastModified()
	{
		$this->setLastModified(\DateTime::createFromFormat('U', $this->file->getMTime()));

		return $this;
	}

	/**
	 * Automatically sets the ETag header according to the checksum of the file.
	 */
	public function setAutoEtag()
	{
		$this->setEtag(sha1_file($this->file->getPathname()));

		return $this;
	}

	/**
	 * Sets the Content-Disposition header with the given filename.
	 *
	 * @param string $disposition      ResponseHeaderBag::DISPOSITION_INLINE or ResponseHeaderBag::DISPOSITION_ATTACHMENT
	 * @param string $filename         Optionally use this filename instead of the real name of the file
	 * @param string $filenameFallback A fallback filename, containing only ASCII characters. Defaults to an automatically encoded filename
	 *
	 * @return BinaryFileResponse
	 */
	public function setContentDisposition($disposition, $filename = '', $filenameFallback = '')
	{
		if ($filename === '') {
			$filename = $this->file->getFilename();
		}

		$dispositionHeader = $this->makeDisposition($disposition, $filename, $filenameFallback);
		$this->setHeader('Content-Disposition', $dispositionHeader);

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function prepare(Request $request)
	{
		$this->headers->set('Content-Length', $this->file->getSize());

		if (!$this->headers->has('Accept-Ranges')) {
			// Only accept ranges on safe HTTP methods
			$this->headers->set('Accept-Ranges', in_array($request->getMethod(), array('GET', 'HEAD')) ? 'bytes' : 'none');
		}

		if (!$this->headers->has('Content-Type')) {
			$this->headers->set('Content-Type', $this->file->getMimeType() ?: 'application/octet-stream');
		}

		$this->offset = 0;
		$this->maxlen = -1;

		if (self::$trustXSendfileTypeHeader && $request->headers->has('X-Sendfile-Type')) {
			// Use X-Sendfile, do not send any content.
			$type = $request->headers->get('X-Sendfile-Type');
			$path = $this->file->getRealPath();
			if (strtolower($type) == 'x-accel-redirect') {
				// Do X-Accel-Mapping substitutions.
				// @link http://wiki.nginx.org/X-accel#X-Accel-Redirect
				foreach (explode(',', $request->headers->get('X-Accel-Mapping', '')) as $mapping) {
					$mapping = explode('=', $mapping, 2);

					if (2 == count($mapping)) {
						$pathPrefix = trim($mapping[0]);
						$location = trim($mapping[1]);

						if (substr($path, 0, strlen($pathPrefix)) == $pathPrefix) {
							$path = $location.substr($path, strlen($pathPrefix));
							break;
						}
					}
				}
			}
			$this->headers->set($type, $path);
			$this->maxlen = 0;
		} elseif ($request->headers->has('Range')) {
			// Process the range headers.
			if (!$request->headers->has('If-Range') || $this->getEtag() == $request->headers->get('If-Range')) {
				$range = $request->headers->get('Range');
				$fileSize = $this->file->getSize();

				list($start, $end) = explode('-', substr($range, 6), 2) + array(0);

				$end = ('' === $end) ? $fileSize - 1 : (int) $end;

				if ('' === $start) {
					$start = $fileSize - $end;
					$end = $fileSize - 1;
				} else {
					$start = (int) $start;
				}

				if ($start <= $end) {
					if ($start < 0 || $end > $fileSize - 1) {
						$this->setStatus(416);
					} elseif ($start !== 0 || $end !== $fileSize - 1) {
						$this->maxlen = $end < $fileSize ? $end - $start + 1 : -1;
						$this->offset = $start;

						$this->setStatus(206);
						$this->headers->set('Content-Range', sprintf('bytes %s-%s/%s', $start, $end, $fileSize));
						$this->headers->set('Content-Length', $end - $start + 1);
					}
				}
			}
		}

		return $this;
	}

	/**
	 * Sends the file.
	 */
	public function sendContent()
	{
		if (!$this->isSuccessful()) {
			parent::sendContent();

			return;
		}

		if (0 === $this->maxlen) {
			return;
		}

		$out = fopen('php://output', 'wb');
		$file = fopen($this->file->getPathname(), 'rb');

		stream_copy_to_stream($file, $out, $this->maxlen, $this->offset);

		fclose($out);
		fclose($file);

	}

	/**
	 * {@inheritdoc}
	 *
	 * @throws \LogicException when the content is not null
	 */
	public function setContent($content)
	{
		if (null !== $content) {
			throw new \LogicException('The content cannot be set on a BinaryFileResponse instance.');
		}
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return false
	 */
	public function getContent()
	{
		return false;
	}

}
