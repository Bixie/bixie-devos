<?php

namespace Bixie\Framework\Utils;

class Collection
{

    /**
     * Finds array in collection by key in array.
     *
     * @param  array $data
     * @param  string $searchKey
     * @param  string $searchValue
     * @return array
     */
    public static function findByKey(array $data, $searchKey, $searchValue)
    {
		$result = (isset($data[$searchKey]) && $data[$searchKey] == $searchValue) ? $data : false;
		if (false === $result) {
			foreach ($data as $keypath => $value) {
				if (is_array($value)) {
					$result = self::findByKey($value, $searchKey, $searchValue);
					if (false !== $result) {
						break;
					}
				}
			}
		}
		return $result;

    }

	/**
	 * @param array $data
	 * @param string $groupKey
	 * @return array
	 */
	public static function groupBy (array $data, $groupKey) {
		$result = array();
		foreach ($data as $key => $value) {
			if (isset($value[$groupKey])) {
				if (!isset($result[$value[$groupKey]])) {
					$result[$value[$groupKey]] = array();
				}
				$result[$value[$groupKey]][] = $value;
			}
		}
		return $result;
	}

	public static function sortByKey (array &$array, $key, $dir = 1) {
		uasort($array, function (array $a, array $b) use ($key, $dir) {
			if ($a[$key] == $b[$key]) {
				return 0;
			}
			return ($a[$key] > $b[$key] ? 1 : -1) * $dir;
		});
	}

}
