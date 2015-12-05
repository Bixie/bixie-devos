<?php

namespace Bixie\Framework\Content;


use Bixie\Framework\Traits\DataTrait;

class Content implements \ArrayAccess  {

	use DataTrait;

	public $title;

	public $content;

	/**
	 * @return mixed
	 */
	public function getTitle () {
		return $this->title;
	}

	/**
	 * @param mixed $title
	 */
	public function setTitle ($title) {
		$this->title = $title;
	}

	/**
	 * @return mixed
	 */
	public function getContent () {
		return $this->content;
	}

	/**
	 * @param mixed $content
	 */
	public function setContent ($content) {
		$this->content = $content;
	}

}