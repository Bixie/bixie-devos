<?php

namespace Bixie\Framework\Routing;

use YOOtheme\Framework\Routing\ResponseProvider as ResponseProviderBase;

class ResponseProvider extends ResponseProviderBase
{

	/**
	 * Returns a JSON response.
	 *
	 * @param  string|array $data
	 * @param  int          $status
	 * @param  array        $headers
	 * @return JsonResponse
	 */
	public function json($data = array(), $status = 200, $headers = array())
	{
		return new JsonResponse($data, $status, $headers);
	}

	/**
	 * Returns a File response.
	 * @param  string|array $data
	 * @param  int          $status
	 * @param  array        $headers
	 * @param null          $contentDisposition
	 * @param bool          $autoEtag
	 * @param bool          $autoLastModified
	 * @return BinaryFileResponse
	 */
	public function file($data = array(), $status = 200, $headers = array(), $contentDisposition = null, $autoEtag = false, $autoLastModified = true)
	{
		return new BinaryFileResponse($data, $status, $headers, $contentDisposition, $autoEtag, $autoLastModified);
	}
}
