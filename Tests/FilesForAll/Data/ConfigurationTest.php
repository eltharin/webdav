<?php

namespace Eltharin\WebdavBundle\Tests\FilesForAll\Data;

use Eltharin\WebdavBundle\FileGetter\WebDavFileGetter;
use Eltharin\WebdavBundle\Interface\AbstractWebDavConfiguration;
use Eltharin\WebdavBundle\Interface\FileGetterInterface;
use Eltharin\WebdavBundle\Routing\RouteData;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\KernelInterface;

class ConfigurationTest extends AbstractWebDavConfiguration
{
	public function __construct(private KernelInterface $appKernel, private RequestStack $requestStack, private string $folderuuid)
	{
	}

	public function getRouteName(): string
	{
		return 'webdavtest';
	}

	public function getRouteData(): RouteData
	{
		return new RouteData('/webdavtest');
	}

	public function getFileGetter(): FileGetterInterface
	{
		$fileGetter = new WebDavFileGetter();
		$fileGetter->setConfig($this->folderuuid . DIRECTORY_SEPARATOR,
			$this->requestStack->getMainRequest()->getSchemeAndHttpHost(),
			'/' . $this->getRouteName());

		return $fileGetter;
	}
}
