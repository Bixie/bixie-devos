<?php

namespace Bixie\Gls\Status\Ftp;

use Bixie\Gls\GlsException;
use Touki\FTP\Connection\Connection;
use Touki\FTP\Exception\FTPException;
use Touki\FTP\FTPFactory;
use Touki\FTP\Model\Directory;
use Touki\FTP\Model\File;

class FtpGls {
	/**
	 * @var \Touki\FTP\FTP
	 */
	protected $ftp;

	/**
	 * FtpGls constructor.
	 */
	public function __construct ($host, $username, $password, $port = 21, $timeout = 10, $passive = true) {

		$connection = new Connection($host, $username, $password, $port, $timeout, $passive);
		$connection->open();
		$factory = new FTPFactory;
		$this->ftp = $factory->build($connection);
	}

	/**
	 * @param string $folder
	 * @return File[]
	 * @throws GlsException
	 */
	public function getList ($folder = '/out') {
		try {

			return $this->ftp->findFiles(new Directory($folder));

		} catch (FTPException $e) {
			throw new GlsException($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
	 * @param      $path
	 * @param      $dest_folder
	 * @param bool $delete
	 * @return string
	 * @throws GlsException
	 */
	public function getFileContents ($path, $dest_folder, $delete = false) {

		try {
			$contents = '';

			$file = $this->ftp->findFileByName(basename($path), new Directory(dirname($path)));

			$dest = $dest_folder . '/' . basename($path);

			if (null !== $file) {

				$this->ftp->download($dest, $file);
				
				if ($contents = file_get_contents($dest) and $delete) {

					$this->ftp->delete($file);

				}

			}
			
			return $contents;

		} catch (FTPException $e) {
			throw new GlsException($e->getMessage(), $e->getCode(), $e);
		}
	}
}