<?php

namespace Bixie\SendCloud;


use Bixie\Devos\Model\Sender\Sender;
use Bixie\Devos\Model\Shipment\ShipmentSendCloud;
use Bixie\Framework\Application;
use Bixie\Framework\ApplicationAware;
use Bixie\SendCloud\Carriers\SendCloud\User;
use Picqer\Carriers\SendCloud\Connection;
use Picqer\Carriers\SendCloud\SendCloudApiException;

class SendCloudApi extends ApplicationAware
{

    /**
     * @var array
     */
    protected $shippingMethods;
    /**
     * @var User
     */
    protected $user;
    /**
     * @var array
     */
    protected $cache = [];

    /**
     * @var SendCloudApi
     */
    protected $sendCloud;

    public function __construct (Application $app) {
        $this->app = $app;
        $connection = new Connection($this['config']['sc_api_key'], $this['config']['sc_api_secret']);
        $connection->setEnvironment($this['config']['sc_api_env']);

        $this->sendCloud = new Carriers\SendCloud\SendCloud($connection);
    }

    /**
     * @param bool $cache
     * @return array
     */
    public function getShippingMethods ($cache = true) {
        if (!$this->shippingMethods) {
            $cache = $this->getCache('%s/%s.shippingmethods.cache', $cache ? (24*60*60) : false);
            if ($cache['valid'] and $methods = json_decode(file_get_contents($cache['file']), true)) {

                $this->shippingMethods = $this->sendCloud->shippingMethods()->collectionFromResult(['shipping_methods' => $methods]);

            } else {
                try {

                    $this->shippingMethods = $this->sendCloud->shippingMethods()->all();

                } catch (SendCloudApiException $e) {
                    $this->app['joomla']->enqueueMessage($e->getMessage(), 'error');
                }
                $this->writeCache($cache['file'], json_encode($this->shippingMethods));

            }
        }
        return $this->shippingMethods;
    }

    /**
     * @param bool $cache
     * @return User
     */
    public function getUser ($cache = true) {
        if (!$this->user) {
            $cache = $this->getCache('%s/%s.user.cache', $cache ? (24*60*60) : false);
            if ($cache['valid'] and $user = json_decode(file_get_contents($cache['file']), true)) {

                $this->user = $this->sendCloud->users()->fromArray($user);

            } else {
                try {

                    $this->user = $this->sendCloud->users()->find();

                } catch (SendCloudApiException $e) {
                    $this->app['joomla']->enqueueMessage($e->getMessage(), 'error');
                }
                $this->writeCache($cache['file'], json_encode($this->user));

            }
        }
        return $this->user;
    }

    /**
     * @return array
     */
    public function getParcels () {
        try {

            return $this->sendCloud->parcels()->all();

        } catch (SendCloudApiException $e) {
            $this->app['joomla']->enqueueMessage($e->getMessage(), 'error');
            return [];
        }
    }

    /**
     * @param ShipmentSendCloud $shipment
     * @return Carriers\SendCloud\Parcel|bool
     */
    public function createShipment (ShipmentSendCloud $shipment) {

        $parcel = $this->sendCloud->parcels($shipment->toArray([
            'shipment' => ['id' => $shipment->getShipment()]
        ], ['parcel']));

        try {

            $parcel->save();
            return $parcel;

        } catch (SendCloudApiException $e) {
            $this->app['joomla']->enqueueMessage($e->getMessage(), 'error');
        }
        return false;

    }

    /**
     * @param ShipmentSendCloud $shipment
     * @param Sender $sender
     */
    public function createLabel (ShipmentSendCloud $shipment, Sender $sender) {
        try {

            $label = $this->sendCloud->labels()->find($shipment->getSendcloudId());

            $shipment->savePdfString($this->app['path.pdf'], $label->labelPrinterContent());
            $shipment->setZplTemplate($label->labelZplContent());
//            $shipment->savePngString($this->app['path.image'], $label->createPngLabel());

        } catch (SendCloudApiException $e) {
            $this->app['joomla']->enqueueMessage($e->getMessage(), 'error');
        }
    }

    /**
     * Gets cache info.
     * @param  string $file
     * @param null|bool|int $ttl
     * @return bool|string
     */
    protected function getCache ($file, $ttl = null) {
        if (!$this->app['path.cache']) {
            return null;
        }

        if (!isset($this->cache[$file])) {
            $key = sha1($this['config']['sc_api_key'] . $this['config']['sc_api_secret'] . $this['config']['sc_api_env']);
            $this->cache[$file] = [
                'key' => $key,
                'file' => sprintf($file, $this->app['path.cache'], $key)
            ];
            $this->cache[$file]['valid'] = file_exists($this->cache[$file]['file']);
            if (false === $ttl) {
                $this->cache[$file]['valid'] = false;
            }
            if ($this->cache[$file]['valid'] && $ttl > 0) {
                $this->cache[$file]['valid'] = filemtime($this->cache[$file]['file']) >= time() + $ttl;
            }
        }

        return $this->cache[$file];
    }

    /**
     * Writes cache file.
     * @param  string $file
     * @param  string $content
     * @throws \RuntimeException
     */
    protected function writeCache ($file, $content) {
        if (!file_put_contents($file, $content)) {
            throw new \RuntimeException("Failed to write cache file ($file).");
        }
    }

}