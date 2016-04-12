<?php

namespace Bixie\Framework\Utils;

class Image {

	/**
	 * @param string $path
	 * @param int $width
	 * @param int $height
	 */
	public static function thumbImage ($path, $width, $height) {
		$image = new \Imagick();
		$image->readImage($path);
		if ($image->getImageHeight() > $height || $image->getImageWidth() > $width) {
			$image->thumbnailImage($width, $height, true);
		}
		$image->setImageFormat("jpg");
		// Set to use jpeg compression
		$image->setImageCompression(\Imagick::COMPRESSION_JPEG);
		// Set compression level (1 lowest quality, 100 highest quality)
		$image->setImageCompressionQuality(75);
		// Strip out unneeded meta data
		$image->stripImage();
		$image->writeImage($path);
	}


}
