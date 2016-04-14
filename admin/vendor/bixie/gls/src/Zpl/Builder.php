<?php

namespace Bixie\Gls\Zpl;


use Zebra\Zpl\Builder as BuilderBase;
use Zebra\Contracts\Zpl\Image as ImageContract;
use Zebra\Zpl\Image;

class Builder extends BuilderBase {

	/**
	 * Add GF command.
	 * @param       $id
	 * @param Image $image
	 * @return static
	 */
	public function imageString($id, Image $image = null)
	{

		if ($image instanceof ImageContract) {

			$bytesPerRow = $image->width();
			$byteCount = $fieldCount = $bytesPerRow * $image->height();

			return sprintf('%s.%d,%d,%d,%s', $id, $byteCount, $fieldCount, $bytesPerRow, $image->toAscii());
		}

		return $this;
	}


}
