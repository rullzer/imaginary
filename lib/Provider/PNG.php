<?php
declare(strict_types=1);
/**
 * @copyright Copyright (c) 2018, Roeland Jago Douma <roeland@famdouma.nl>
 *
 * @author Roeland Jago Douma <roeland@famdouma.nl>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\Imaginary\Provider;

use OCP\Files\File;
use OCP\Files\FileInfo;
use OCP\Http\Client\IClientService;
use OCP\IConfig;
use OCP\IImage;
use OCP\Image;
use OCP\Preview\IProviderV2;

class PNG implements IProviderV2 {

	/** @var string */
	private $host;
	/** @var IConfig */
	private $config;
	/** @var IClientService */
	private $clientService;

	public function __construct(IConfig $config, IClientService $clientService) {
		$this->config = $config;
		$this->clientService = $clientService;
		$this->host = 'http://localhost:9001';
	}

	public function isAvailable(FileInfo $file): bool {
		return $file->getMimetype() === 'image/png';
	}

	public function getThumbnail(File $file, int $maxX, int $maxY): ?IImage {
		$client = $this->clientService->newClient();

		$stream = $file->fopen('r');
		$res = $client->post($this->host . '/info', [
			'body' => $stream,
		]);

		$body = $res->getBody();
		$dataBody = json_decode($body, true);


		$client = $this->clientService->newClient();
		$stream = $file->fopen('r');

		$res = $client->post($this->host.'/resize?width='. $dataBody['width'], [
			'body' => $stream,
		]);

		$body = $res->getBody();
		$i = new Image();
		$i->loadFromData($body);

		return $i;
	}

	private function calculateWidth(int $maxX, int $maxY, int $curX, int $curY): int {

	}

	public function getMimeType(): string {
		return '/image\/png/';
	}


}
