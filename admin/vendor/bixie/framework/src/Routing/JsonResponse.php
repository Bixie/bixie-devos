<?php

namespace Bixie\Framework\Routing;


use YOOtheme\Framework\Routing\JsonResponse as JsonResponseBase;

class JsonResponse extends JsonResponseBase
{

/**
	 * Constructor.
	 *
	 * @param mixed  $data
	 * @param int    $status
	 * @param array  $headers
	 */
	public function __construct($data = null, $status = 200, $headers = array())
	{
		parent::__construct(null, $status, $headers);

		$this->options = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_NUMERIC_CHECK;
		$this->setData($data);
	}


}
