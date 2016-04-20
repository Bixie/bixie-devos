<?php

namespace Bixie\Framework\Utils;


use Symfony\Component\HttpFoundation\File\File;

class Image {

	/**
	 * @param File $file
	 * @param int  $width
	 * @param int  $height
	 * @return File
	 */
	public static function thumbImage (File $file, $width, $height) {
		$image = new \Imagick();
		$image->readImage($file->getPathname());
		if ($image->getImageHeight() > $height || $image->getImageWidth() > $width) {
			$image->thumbnailImage($width, $height, true);
		}

		$image->setImageBackgroundColor(new \ImagickPixel('white'));
		$image->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);
		$image->setImageFormat("jpg");
		// Set to use jpeg compression
		$image->setImageCompression(\Imagick::COMPRESSION_JPEG);
		// Set compression level (1 lowest quality, 100 highest quality)
		$image->setImageCompressionQuality(75);
		// Strip out unneeded meta data
		$image->stripImage();
		$thumb_path = $file->getPathname();
		if ($file->getExtension() !== 'jpg') {
			$thumb_path = $file->getPath() . '/' . $file->getBasename('.'.$file->getExtension()) . '.jpg';
		}

		$image->writeImage($thumb_path);
		$image->clear();
		$image->destroy();
		return new File($thumb_path);
	}


}
