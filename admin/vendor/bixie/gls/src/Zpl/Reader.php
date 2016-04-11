<?php

namespace Bixie\Gls\Zpl;


use Zebra\Zpl\Builder;

class Reader
{
    /**
     * ZPL string.
     *
     * @var string
     */
    protected $zpl_raw = '';

	/**
	 * @var Builder
	 */
	protected $builder;

	/**
     * Create a new instance statically.
     *
     * @return self
     */
    public static function start($zpl_raw)
    {
        return new static($zpl_raw);
    }

	/**
	 * Reader constructor.
	 * @param string $zpl_raw
	 */
	public function __construct ($zpl_raw)
	{
		$this->zpl_raw = $zpl_raw;
		$this->builder = new Builder();
	}


	public function build ()
	{

		return $this->builder;
	}

}
