<?php
/**
 * @package    Joomla.Cli
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// We are a valid entry point.
const _JEXEC = 1;

if (!defined('_JDEFINES')) {
	$base = str_replace('/administrator/components/com_bix_devos/cli', '', __DIR__);
	$dev = false;
	if (!file_exists($base . '/includes/defines.php')) { //symlink in dev environment
		$dev = true;
		$base = str_replace('/bixie-devos/admin/cli', '/html', __DIR__);
	}
	define('JPATH_BASE', $base);
	define('DEV_ENV', $dev);
	require_once JPATH_BASE . '/includes/defines.php';
}

// Get the framework.
require_once JPATH_LIBRARIES . '/import.legacy.php';

// Bootstrap the CMS libraries.
require_once JPATH_LIBRARIES . '/cms.php';

// Configure error reporting to maximum for CLI output.
error_reporting(E_ALL);
ini_set('display_errors', 1);


/**
 * A command line cron job to attempt to remove files that should have been deleted at update.
 * Arguments:
 * @arg    string -a action
 * @arg    bool --log log to file
 *         
 * Example `php /var/www/bixie-devos/admin/cli/cronjobs.php -a=sync_gls --log`
 * @since  3.0
 */
class SyncGlsCli extends JApplicationCli {
	/**
	 * @var string
	 */
	protected $clientFolder = '/../bixie-devos-client';
	/**
	 * @var array
	 */
	protected $clientConfig = [
		'api_url' => 'http://www.devosdiensten.nl/component/bix_devos/',
		'api_username' => 'cronjob',
		'api_secret' => 'cronjobkey_96w53hksll7f48'
	];
	/**
	 * @var \Bixie\DevosClient\DevosClient
	 */
	protected $client;
	/**
	 * @var bool
	 */
	protected $logToFile = false;
	/**
	 * @var array
	 */
	protected $logs = [];

	/**
	 * Entry point for CLI script
	 * @return  void
	 * @since   3.0
	 */
	public function doExecute () {
		
		$this->logToFile = $this->input->getBool('log', false);
		//no app and db available! Use client to curl in
		if (DEV_ENV) {
			$this->clientConfig['api_url'] = 'http://www.devos.dev/component/bix_devos/';
		}
		$client_main = JPATH_BASE . $this->clientFolder . '/main.php';
		if (!file_exists($client_main)) {
			$this->out("Client niet gevonden in $client_main!");
			$this->close(500);
		}
		include $client_main;

		$this->client = new \Bixie\DevosClient\DevosClient($this->clientConfig);


		if ($action = $this->input->get('a', '')) {
			$this->out("Task $action triggered.");
			switch ($action) {
				case 'sync_gls':
					$this->syncGls();
					break;
				default:
					break;
			}
		}

	}

	/**
	 * SYnc parcels via FTP syncing GLS
	 */
	protected function syncGls () {

		$response = $this->client->get('/api/glstracking/sync');

		if ($responseData = $response->getData()) {

			if (!count($responseData['trackings'])) {
				$this->out("Geen nieuwe bestanden gevonden.");
			}

			foreach ($responseData['trackings'] as $tracking) {
				$this->out("Bestand {$tracking['filename']} verwerkt");
				if ($tracking['errors']) {
					$this->out("Fouten:" . implode(', ', $tracking['errors']));
				}
			}
		} else {
			$this->out($response->getError());
		}

	}

	/**
	 * @param string $text
	 * @param bool   $nl
	 * @return $this
	 */
	public function out ($text = '', $nl = true) {
		if ($this->logToFile) {
			return $this->log($text);
		}
		parent::out($text, $nl);
		return $this;
	}

	/**
	 * @param string $text
	 * @return $this
	 */
	public function log ($text = '') {
		$this->logs[] = $text;
		return $this;
	}

	function __destruct () {
		if ($this->logToFile && count($this->logs)) {
			$now = new \DateTime('', new DateTimeZone('Europe/Amsterdam'));
			$path = JPATH_BASE . '/../logs/cronjobs_logs' . $now->format('YM');
			if (!is_dir(JPATH_BASE . '/../logs')) {
				mkdir(JPATH_BASE . '/../logs', 0644);
			}
			if (!is_dir($path)) {
				mkdir($path, 0644);
			}
			array_unshift($this->logs, "Log added " . $now->format('Y-m-d H:i:s'));
			file_put_contents($path . '/cronlog_week' . $now->format('W') . '.txt', implode("\n", $this->logs) . "\n", FILE_APPEND);
		}
	}

}

// Instantiate the application object, passing the class name to JCli::getInstance
// and use chaining to execute the application.
JApplicationCli::getInstance('SyncGlsCli')->execute();
