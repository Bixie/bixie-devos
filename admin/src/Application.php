<?php

namespace Bixie\Devos;

use Bixie\Devos\Model\Address\AddressProvider;
use Bixie\Devos\Model\GlsTracking\GlsTrackingProvider;
use Bixie\Devos\Model\Sender\SenderProvider;
use Bixie\Devos\Model\Shipment\ShipmentGlsProvider;
use Bixie\Devos\Config\Settings;
use Bixie\Devos\Model\Shipment\ShipmentSendCloudProvider;
use Bixie\Framework\Routing\ResponseListener;
use Bixie\Framework\Routing\ResponseProvider;
use Bixie\Framework\User\User;
use Bixie\Devos\User\UserProvider;
use Bixie\Framework\Utils\Mail;
use Bixie\Gls\Gls;
use Bixie\Gls\Status\Ftp\FtpGls;
use Bixie\Gls\Status\Status;
use Bixie\Framework\Application as BaseApplication;
use Bixie\Framework\Event\EventSubscriberInterface;
use Bixie\SendCloud\SendCloudApi;

class Application extends BaseApplication implements EventSubscriberInterface
{

    /**
     * Constructor.
     *
     * @param array $values
     */
    public function __construct(array $values = array())
    {
        parent::__construct($values);

		$this['plugins']->addPath($this['path'].'/plugins/*/plugin.php');
		$this['plugins']->addPath($this['path.vendor'].'/bixie/framework/plugins/*/plugin.php');

		//configstorage
		$this['settings'] = function ($app) {
			return new Settings($app['db'], 'config');
		};
		$this['config.fetch'] = function ($app) {
			return $app['settings']->toArray();
		};
		$this['config.save'] = function ($app) {
			$app['settings']->add($app['config']->toArray());
			return $app['config'];
		};

		//providers
		$this['shipmentgls']   = new ShipmentGlsProvider($this);
		$this['shipmentsendcloud']   = new ShipmentSendCloudProvider($this);
		$this['address']   = new AddressProvider($this);
		$this['sender']   = new SenderProvider($this);
		$this['glstracking'] = new GlsTrackingProvider($this);

		$this->extend('locator', function ($locator, $app) {
            return $locator->addPath('', $app['path']);
        });

        $this->on('boot', function ($event, $app) {

			$app['response'] = function ($app) {
				return new ResponseProvider($app['url']);
			};

		});

		$this['events']->subscribe($this);
        $this['events']->subscribe(new ResponseListener);
    }

    public function init()
    {
        // controller
        $this['controllers']->add('Bixie\Devos\Controller\DashboardController');
        $this['controllers']->add('Bixie\Devos\Controller\GlsTrackingController');
        $this['controllers']->add('Bixie\Devos\Controller\SiteController');
        $this['controllers']->add('Bixie\Devos\Controller\SenderApiController');
        $this['controllers']->add('Bixie\Devos\Controller\AddressApiController');
        $this['controllers']->add('Bixie\Devos\Controller\ShipmentApiController');
        $this['controllers']->add('Bixie\Devos\Controller\SendCloudApiController');
        $this['controllers']->add('Bixie\Devos\Controller\GlsTrackingApiController');


        // combine assets
        if (!$this['debug']) {
//            $this['styles']->combine('wk-styles', 'widgetkit-*' , array('CssImportResolver', 'CssRewriteUrl', 'CssImageBase64'));
//            $this['scripts']->combine('wk-scripts', 'widgetkit-*')->combine('uikit', 'uikit*')->combine('angular', 'angular*')->combine('application', 'application{,-translator,-templates}');
        }

		//gls
		$this['sendcloud'] = new SendCloudApi($this);
		//gls
		$this['gls'] = new Gls($this);
		//override userprovider
		$this['users'] = function ($app) {
			return new UserProvider($app, $app['component'], isset($app['permissions']) ? $app['permissions'] : array());
		};
		//setup ftp connection ftp-status
		$this['gls.ftp'] = function ($app) {
			return function ($file = null) use ($app) {
				$ftp = new FtpGls(
					$app['config']['gls_ftp_host'],
					$app['config']['gls_ftp_user'],
					$app['config']['gls_ftp_pass'],
					$app['config']['gls_ftp_port']
				);
				if ($file) {
					return $ftp->getFileContents($file, $app['path.xml']);
				}
				return $ftp;
			};
		};
		$this['gls.status'] = function ($app) {
		    return new Status($app);
		};
        //mail
        $this['mail'] = function ($app) {
            $bcc = array_map('trim', explode(';', $app['config']['admin_mails']));
            return new Mail($bcc, $app['path'] . '/views/mail/mailtemplate.html');
        };
		/** @var User $user */
		$user = $this['users']->get();
		$config = [
			'csrf' =>  $this['csrf']->generate(),
			'locale' => 'nl-NL',
			'user' => [
				'id' => $user->getId(),
				'username' => $user->getUsername(),
				'name' => $user->getName(),
				'bedrijfsnaam' => $user['bedrijfsnaam'],
				'gls_customer_number' => $user['gls_customer_number'] ? : $this['config']['gls_customer_number'],
				'klantnummer' => $user['klantnummer'],
				'zpl_printer' => $user['zpl_printer'],
				'pdf_printer' => $user['pdf_printer']
			],
			'current' => \JUri::current(),
			'url' => 'index.php',
			'countries' => $this['countries']
		];
		$this['scripts']->add('devos-config', sprintf('var $config = %s;', json_encode($config)), '', 'string');

		$this['scripts']->register('vue', 'assets/js/vue.js');
		$this['scripts']->register('uikit', 'vendor/assets/uikit/js/uikit.min.js');
		$this['scripts']->register('uikit-tooltip', 'vendor/assets/uikit/js/components/tooltip.min.js', ['uikit']);
		$this['scripts']->register('uikit-notify', 'vendor/assets/uikit/js/components/notify.min.js', ['uikit']);
		$this['scripts']->register('uikit-upload', 'vendor/assets/uikit/js/components/upload.min.js', ['uikit']);
		$this['scripts']->register('uikit-pagination', 'vendor/assets/uikit/js/components/pagination.min.js', ['uikit']);
		$this['scripts']->register('uikit-sticky', 'vendor/assets/uikit/js/components/sticky.js', ['uikit']);
		$this['scripts']->register('uikit-form-select', 'vendor/assets/uikit/js/components/form-select.js', ['uikit']);
		$this['scripts']->register('uikit-lightbox', 'vendor/assets/uikit/js/components/lightbox.js', ['uikit']);
		$this['scripts']->register('uikit-slideset', 'vendor/assets/uikit/js/components/slideset.js', ['uikit']);
		$this['scripts']->register('uikit-slider', 'vendor/assets/uikit/js/components/slider.js', ['uikit']);
		$this['scripts']->register('uikit-slideshow', 'vendor/assets/uikit/js/components/slideshow.js', ['uikit']);

		// site event
        if (!$this['admin']) {
            $this->trigger('init.site', array($this));
        }
    }

    public function initSite()
    {
        // scripts
		\plgSystemBixsystem::loadJsAssets([], true, $this);
    }

    public function initAdmin()
    {
		$this['styles']->add('devos-admin', 'assets/css/admin.css');

    }

    public static function getSubscribedEvents()
    {
        return array(
            'init'        => array('init', -5),
            'init.site'   => 'initSite',
            'init.admin'  => 'initAdmin',
//            'view.render' => 'viewRender'
        );
    }
}
