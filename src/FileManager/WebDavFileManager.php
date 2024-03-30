<?php

namespace Eltharin\WebdavBundle\FileManager;

use DateInterval;
use DateTimeImmutable;
use Eltharin\WebdavBundle\Exception\BadRequestException;
use Eltharin\WebdavBundle\Exception\FileLockedException;
use Eltharin\WebdavBundle\Interface\FileManagerInterface;
use Eltharin\WebdavBundle\Object\FileInfos;
use Eltharin\WebdavBundle\Object\FolderInfos;
use Eltharin\WebdavBundle\Object\LockInfos;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class WebDavFileManager implements FileManagerInterface
{
	private string $rootPath = '';
	private string $url = '';
	private string $urlRootPath = '';
	private $lockSuffix = '~.lock';
	protected $methods = ['OPTIONS', 'MKCOL', 'GET', 'PUT', 'DELETE', 'COPY', 'MOVE', 'PROPFIND', 'HEAD', 'POST', 'TRACE', 'LOCK', 'UNLOCK'/* 'PROPPATCH', 'ORDERPATCH' */];

	public function setConfig(string $rootPath, string $url, string $urlRootPath): void
	{
		$this->rootPath = $rootPath;
		$this->url = $url;
		$this->urlRootPath = $urlRootPath;
	}

	public function getMethods(): array
	{
		return $this->methods;
	}

	public function getFileContent(string $file): string
	{
		return file_get_contents($this->rootPath . DIRECTORY_SEPARATOR . $file);
	}

	public function getFileInfos(string $file): FileInfos
	{
		$info = new FileInfos('path', 'name', 'mime-type', 'size', 'datecreation', 'datemodif');
		$info->setSize(filesize($this->rootPath . DIRECTORY_SEPARATOR . $file))
			->setPath($this->urlRootPath . DIRECTORY_SEPARATOR . $file)
			->setName(basename($this->rootPath . DIRECTORY_SEPARATOR . $file))
			->setDatecreation(DateTimeImmutable::createFromFormat('U', filectime($this->rootPath . DIRECTORY_SEPARATOR . $file)))
			->setDatemodif(DateTimeImmutable::createFromFormat('U', filemtime($this->rootPath . DIRECTORY_SEPARATOR . $file)))
			->setMimeType((new MimeTypes())->guessMimeType($this->rootPath . DIRECTORY_SEPARATOR . $file));

		if (($lockInfos = $this->getLockInfos($file)) !== null)
		{
			$info->setLockInfos($lockInfos);
		}

		return $info;
	}

	public function getFolderInfos(string $folder, int $depth): FolderInfos
	{
		$info = new FolderInfos('path', 'name', 'mime-type', 'size', 'datecreation', 'datemodif');
		$info->setPath(rtrim($this->urlRootPath . DIRECTORY_SEPARATOR . $folder, DIRECTORY_SEPARATOR))
			->setName(basename($this->rootPath . DIRECTORY_SEPARATOR . $folder))
			->setDatecreation(DateTimeImmutable::createFromFormat('U', filectime($this->rootPath . DIRECTORY_SEPARATOR . $folder)))
			->setDatemodif(DateTimeImmutable::createFromFormat('U', filemtime($this->rootPath . DIRECTORY_SEPARATOR . $folder)))
		;

		if (($lockInfos = $this->getLockInfos($folder)) !== null)
		{
			$info->setLockInfos($lockInfos);
		}

		if ($depth == 1)
		{
			$finder = new Finder();
			foreach ($finder->in($this->rootPath . DIRECTORY_SEPARATOR . $folder)->depth(0) as $sub)
			{
				$path = ($folder == '' ? '' : $folder . '/') . basename($sub);

				if ($sub->isDir())
				{
					$info->addRessource($this->getFolderInfos($path, 0));
				}
				elseif ($sub->isFile() && !str_ends_with($sub->getBasename(), $this->lockSuffix))
				{
					$info->addRessource($this->getFileInfos($path));
				}
			}
		}

		return $info;
	}

	public function checkFileType($path): ?string
	{
		if (!file_exists($this->rootPath . DIRECTORY_SEPARATOR . $path) || str_ends_with($path, $this->lockSuffix))
		{
			return null;
		}
		elseif (is_file($this->rootPath . DIRECTORY_SEPARATOR . $path))
		{
			return FileManagerInterface::TYPE_FILE;
		}

		return FileManagerInterface::TYPE_FOLDER;
	}

	public function checkAutorization(string $path, AuthorizationCheckerInterface $security): void
	{
	}

	public function createDirectory($path): bool
	{
		return @mkdir($this->rootPath . DIRECTORY_SEPARATOR . $path, recursive: true);
	}

	public function appendFile($path, $content): bool
	{
		return file_put_contents($this->rootPath . DIRECTORY_SEPARATOR . $path, $content) !== false;
	}

	protected function getDestinationPath($destination): string
	{
		if (str_starts_with($destination, $this->url))
		{
			$destination = substr($destination, strlen($this->url));
		}

		if (str_starts_with($destination, $this->urlRootPath))
		{
			$destination = substr($destination, strlen($this->urlRootPath) + 1);
		}

		return $destination;
	}

	public function moveDirectory($path, $destination): bool
	{
		$destPath = $this->getDestinationPath($destination);
		@mkdir(dirname($this->rootPath . DIRECTORY_SEPARATOR . $destPath), recursive: true);

		return rename($this->rootPath . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR, $this->rootPath . DIRECTORY_SEPARATOR . $destPath . DIRECTORY_SEPARATOR);
	}

	public function moveFile($path, $destination): bool
	{
		$destPath = $this->getDestinationPath($destination);

		@mkdir(dirname($this->rootPath . DIRECTORY_SEPARATOR . $destPath), recursive: true);

		return @rename($this->rootPath . DIRECTORY_SEPARATOR . $path, $this->rootPath . DIRECTORY_SEPARATOR . $destPath);
	}

	public function deleteDirectory($path): bool
	{
		$objects = scandir($this->rootPath . DIRECTORY_SEPARATOR . $path);

		foreach (scandir($this->rootPath . DIRECTORY_SEPARATOR . $path) as $sub)
		{
			if ($sub != '.' && $sub != '..')
			{
				if (filetype($this->rootPath . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $sub) == 'dir')
				{
					$this->deleteDirectory($path . DIRECTORY_SEPARATOR . $sub);
				}
				else
				{
					$this->deleteFile($path . DIRECTORY_SEPARATOR . $sub);
				}
			}
		}

		return @rmdir($this->rootPath . DIRECTORY_SEPARATOR . $path);
	}

	public function deleteFile($path): bool
	{
		return @unlink($this->rootPath . DIRECTORY_SEPARATOR . $path);
	}

	public function getLockInfos($path): ?LockInfos
	{
		if (!file_exists($this->rootPath . DIRECTORY_SEPARATOR . $path . $this->lockSuffix))
		{
			return null;
		}
		$lockData = json_decode(file_get_contents($this->rootPath . DIRECTORY_SEPARATOR . $path . $this->lockSuffix));

		$lockDataTimeOut = new DateTimeImmutable($lockData->timeout);

		if ((new DateTimeImmutable()) > $lockDataTimeOut)
		{
			unlink($this->rootPath . DIRECTORY_SEPARATOR . $path . $this->lockSuffix);

			return null;
		}

		if ($lockData === null)
		{
			return null;
		}

		$lockInfos = new LockInfos();

		$lockInfos->setOwner($lockData->owner)
			->setToken($lockData->token)
			->setTimeOut($lockDataTimeOut);

		return $lockInfos;
	}

	public function lockFile($path, $owner, $timeout): LockInfos
	{
		if ($this->getLockInfos($path) !== null)
		{
			throw new FileLockedException($path);
		}

		if (str_starts_with($timeout, 'Second-') && is_numeric($nbSeconds = substr($timeout, 7)))
		{
			$timeout = (new DateTimeImmutable())->add(new DateInterval('PT' . $nbSeconds . 'S'));
		}
		else
		{
			$timeout = new DateTimeImmutable(LockInfos::INFINTE_DATE);
		}

		$lockData = new LockInfos();
		$lockData->setOwner($owner)
			->setTimeOut($timeout)
			->setToken(sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535)));

		file_put_contents($this->rootPath . DIRECTORY_SEPARATOR . $path . $this->lockSuffix, json_encode([
			'owner' => $lockData->getOwner(),
			'token' => $lockData->getToken(),
			'timeout' => $lockData->getTimeOut()->format('Y-m-d H:i:s'),
		]));

		return $lockData;
	}

	public function lockFolder($path, $owner, $timeout): LockInfos
	{
		if ($this->getLockInfos($path) !== null)
		{
			throw new FileLockedException($path);
		}

		if (str_starts_with($timeout, 'Second-') && is_numeric($nbSeconds = substr($timeout, 7)))
		{
			$timeout = (new DateTimeImmutable())->add(new DateInterval('PT' . $nbSeconds . 'S'));
		}
		else
		{
			$timeout = new DateTimeImmutable(LockInfos::INFINTE_DATE);
		}

		$lockData = new LockInfos();
		$lockData->setOwner($owner)
			->setTimeOut($timeout)
			->setToken(sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535)));

		file_put_contents($this->rootPath . DIRECTORY_SEPARATOR . $path . $this->lockSuffix, json_encode([
			'owner' => $lockData->getOwner(),
			'token' => $lockData->getToken(),
			'timeout' => $lockData->getTimeOut()->format('Y-m-d H:i:s'),
		]));

		return $lockData;
	}

	public function unlockFile($path, $token): bool
	{
		if (($lockInfos = $this->getLockInfos($path)) === null)
		{
			throw new BadRequestException('no lockinfos');
		}
		if ($token != '<opaquelocktoken:' . $lockInfos->getToken() . '>')
		{
			throw new BadRequestException('BadTokenFormat');
		}

		unlink($this->rootPath . DIRECTORY_SEPARATOR . $path . $this->lockSuffix);

		return true;
	}

	public function unlockFolder($path, $token): bool
	{
		if (($lockInfos = $this->getLockInfos($path)) === null)
		{
			throw new BadRequestException('no lockinfos');
		}

		if ($token != '<opaquelocktoken:' . $lockInfos->getToken() . '>')
		{
			throw new BadRequestException('BadToken');
		}

		unlink($this->rootPath . DIRECTORY_SEPARATOR . $path . $this->lockSuffix);

		return true;
	}
}
