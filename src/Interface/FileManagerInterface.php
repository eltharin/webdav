<?php

namespace Eltharin\WebdavBundle\Interface;

use Eltharin\WebdavBundle\Object\FileInfos;
use Eltharin\WebdavBundle\Object\FolderInfos;
use Eltharin\WebdavBundle\Object\LockInfos;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

#[AutoconfigureTag()]
interface FileManagerInterface
{
	public const TYPE_FOLDER = 'folder';
	public const TYPE_FILE = 'file';

	public function setConfig(string $rootPath, string $url, string $urlRootPath): void;

	public function getMethods(): array;

	public function getFileContent(string $file): string;

	public function getFileInfos(string $file): FileInfos;

	public function getFolderInfos(string $folder, int $depth): FolderInfos;

	public function checkFileType($path): ?string;

	public function checkAutorization(string $path, AuthorizationCheckerInterface $security): void;

	public function createDirectory($path): bool;

	public function appendFile($path, $content): bool;

	public function moveDirectory($path, $destination): bool;

	public function moveFile($path, $destination): bool;

	public function deleteDirectory($path): bool;

	public function deleteFile($path): bool;

	public function getLockInfos($path): ?LockInfos;

	public function lockFile($path, $owner, $timeout): LockInfos;

	public function lockFolder($path, $owner, $timeout): LockInfos;

	public function unlockFile($path, $token): bool;

	public function unlockFolder($path, $token): bool;
}
