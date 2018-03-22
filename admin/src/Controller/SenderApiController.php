<?php

namespace Bixie\Devos\Controller;

use Bixie\Devos\Apihost\Apitoken\Exception\ApitokenException;
use Bixie\Devos\Model\Sender\Sender;
use Bixie\Framework\Utils\Image;
use Bixie\Framework\Utils\Query;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\FileBag;
use Bixie\Framework\Routing\Controller;
use Bixie\Framework\Routing\Exception\HttpException;

class SenderApiController extends Controller {

	public function sendersAction ($inactive = false) {
		$return = new \ArrayObject;

		$query = Query::query('@dv_sender', '*');
		if (!$this['admin']) $query->where('user_id = :user_id', ['user_id' => $this['users']->get()->getId()]);
		if (!$inactive) $query->where('state = 1');
		$query->orderBy('sender_name_1', 'ASC');
		$return['senders'] = $this['sender']->query((string) $query, $query->getParams(), 0, 0);

		return $this['response']->json($return);

	}

	public function uploadSenderAction () {
		$return = new \ArrayObject([
			'error' => false,
			'file_name' => '',
			'path' => ''
		]);

		$path = $this['path.image'] . '/senders/';
		$fileInfo = [];
		try {

			if ($files = (new FileBag($_FILES))->get('files')) {

				/** @var UploadedFile $file */
				foreach ($files as $file) {

					if (!$file->isValid()) {
						throw new \InvalidArgumentException(sprintf('Bestand niet geldig. (%s)', $file->getErrorMessage()));
					}

					if (!$ext = $file->guessExtension() or !in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
						throw new \InvalidArgumentException('Bestandsextensie niet toegestaan.');
					}

					if (!$size = $file->getClientSize() or $size > (3 * 1024 * 1024)) {
						throw new \InvalidArgumentException('Bestand is te groot.');
					}
					//give file unique name
					$localFile = $file->move($path, sprintf('%d%d-%s',
						(microtime(true) * 10000), rand(), preg_replace("/[^a-zA-Z0-9\.]/", "-", $file->getClientOriginalName())));

					$localFile = Image::thumbImage($localFile, 178, 62);

					$fileInfo[] = [
						'name' => $file->getClientOriginalName(),
						'size' => $localFile->getSize(),
						'path' => str_replace(JPATH_ROOT, '', $localFile->getPathname()),
					];
				}

				$uploaded = reset($fileInfo);

				$return['file_name'] = $uploaded['name'];
				$return['file_size'] = $uploaded['size'];
				$return['path'] = $uploaded['path'];
			} else {

				$return['error'] = 'Geen bestanden verzonden.';

			}

		} catch (\Exception $e) {

			$return['error'] = $e->getMessage();

		}

		return $this['response']->json($return);
	}

	public function saveSenderAction ($data) {
		$status = !isset($data['id']) || !$data['id'] ? 201 : 200;
		$return = new \ArrayObject;

		$data['user_id'] = $this['users']->get()->getId();

		if ($data = $this['sender']->save($data)) {
			$return['sender'] = $data;

			return $this['response']->json($return, $status);
		}

		throw new HttpException(400);
	}

	public function setdefaultSenderAction ($id) {

		$user = $this['users']->get();

		if ($object = $this['sender']->find($id)) {

			if ($object->getUserId() != $user->getId()) {
				throw new HttpException(403, 'Geen toegang voor deze gebruiker');
			}

			$this['sender']->setDefault($id, $user->getId());


			return $this->sendersAction(true);

		}

		throw new HttpException(400);
	}

	public function deleteSenderAction ($id) {
		if ($this['sender']->delete($id)) {
			return $this['response']->json(null, 204);
		}
		throw new HttpException(400);
	}


	public static function getRoutes () {
		return array(
			array('/api/sender', 'sendersAction', 'GET', array('access' => 'client_devos')),
			array('/api/sender/:id', 'getSenderAction', 'GET', array('access' => 'client_devos')),
			array('/api/sender/upload', 'uploadSenderAction', 'POST', array('access' => 'client_devos')),
			array('/api/sender/save', 'saveSenderAction', 'POST', array('access' => 'client_devos')),
			array('/api/sender/setdefault', 'setdefaultSenderAction', 'POST', array('access' => 'client_devos')),
			array('/api/sender/:id', 'deleteSenderAction', 'DELETE', array('access' => 'client_devos'))
		);
	}
}
