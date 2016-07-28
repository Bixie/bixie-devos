<?php


namespace Bixie\Framework\Utils;



use Bixie\Framework\Application;

class Cms {
	/**
	 * @param           $url
	 * @param bool|true $xhtml
	 * @param null      $ssl
	 * @return string
	 */
	public static function route ($url, $xhtml = true, $ssl = null) {
		return \JRoute::_($url, $xhtml, $ssl);
	}

	public static function getArticleById (Application $app, $id) {
		$query = Query::query('@content', 'title, introtext AS content, attribs AS data')
			->where('id = :id', compact('id'));

		return $app['db']->fetchObject((string) $query, $query->getParams(), 'Bixie\Framework\Content\Content');
	}

}