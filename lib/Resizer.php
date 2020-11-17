<?php
declare(strict_types=1);
/**
 * @copyright Copyright (c) 2020, Roeland Jago Douma <roeland@famdouma.nl>
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

namespace OCA\PreviewImaginary;

use OCP\Files\File;
use OCP\Files\SimpleFS\ISimpleFile;
use OCP\Http\Client\IClientService;
use OCP\Image;

class Resizer {
	/** @var IClientService */
	private $clientService;

	/** @var string */
	private $host;

	public function __construct(IClientService $clientService) {
		$this->clientService = $clientService;
		$this->host = 'http://localhost:9001';
	}

	public function resize(ISimpleFile $file, int $width, int $height): string {
		$client = $this->clientService->newClient();

		$stream = $file->read();
		$res = $client->post($this->host . '/resize', [
			'nextcloud' => [
				'allow_local_address' => true,
			],
			'query' => [
				'width' => $width,
				'height' => $height,
			],
			'body' => $stream,
		]);



		$res = $res->getBody();

		$path = '/tmp/new.' . $width . '.' . $height . '.' . $file->getName();
		$write = file_put_contents($path, $res);

		return  $res;
	}
}
