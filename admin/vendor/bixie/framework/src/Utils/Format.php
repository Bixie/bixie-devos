<?php
/* *
 *	Bixie Printshop
 *  Tools.php
 *	Created on 22-8-2015 17:35
 *  
 *  @author Matthijs
 *  @copyright Copyright (C)2015 Bixie.nl
 *
 */


namespace Bixie\Framework\Utils;


class Format {
	/**
	 * @param       $value
	 * @param array $options
	 * @return string
	 */
	public static function money ($value, $options = array()) {
		return '€ ' . $value;
	}

}