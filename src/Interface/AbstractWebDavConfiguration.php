<?php

namespace Eltharin\WebdavBundle\Interface;

use Eltharin\WebdavBundle\Routing\RouteData;

#[AutoconfigureTag()]
abstract class AbstractWebDavConfiguration
{
	protected ?SecurityManagerInterface $securityManager = null;

	abstract public function getRouteData(): RouteData;

	abstract public function getFileManager(): FileManagerInterface;

	public function getRouteName(): string
	{
		return str_replace('\\', '_', get_class($this));
	}

	public function getSecurityManager(): ?SecurityManagerInterface
	{
		return $this->securityManager;
	}

	public function getController(): string
	{
		return '';
	}
}
