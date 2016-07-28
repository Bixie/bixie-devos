<?php

namespace Bixie\Framework\View\Asset;

use Bixie\Framework\View\Loader\LoaderInterface;

class AssetFactory
{
    /**
     * @var LoaderInterface
     */
    protected $loader;

    /**
     * @var array
     */
    protected $types = array(
        'file'     => 'Bixie\Framework\View\Asset\FileAsset',
        'string'   => 'Bixie\Framework\View\Asset\StringAsset',
        'template' => 'Bixie\Framework\View\Asset\TemplateAsset'
    );

    /**
     * Constructor.
     *
     * @param LoaderInterface $loader
     */
    public function __construct(LoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    /**
     * Create an asset instance.
     *
     * @param  string $name
     * @param  mixed  $source
     * @param  mixed  $dependencies
     * @param  mixed  $options
     * @return AssetInterface
     * @throws \InvalidArgumentException
     */
    public function create($name, $source, $dependencies = array(), $options = array())
    {
        if (is_string($dependencies)) {
            $dependencies = array($dependencies);
        }

        if (is_string($options)) {
            $options = array('type' => $options);
        }

        if (!isset($options['type'])) {
            $options['type'] = 'file';
        }

        if ($options['type'] == 'file') {
            $options['path'] = $this->loader->load($source);
        }

        if (isset($this->types[$options['type']])) {

            $class = $this->types[$options['type']];

            return new $class($name, $source, $dependencies, $options);
        }

        throw new \InvalidArgumentException('Unable to determine asset type.');
    }

    /**
     * Registers an asset type.
     *
     * @param  string $name
     * @param  string $class
     * @return self
     */
    public function register($name, $class)
    {
        $this->types[$name] = $class;

        return $this;
    }
}
