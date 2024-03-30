<?php

namespace Eltharin\WebdavBundle\Service;

use Eltharin\WebdavBundle\Exception\ConfigrationNotFoundException;
use Eltharin\WebdavBundle\Interface\AbstractWebDavConfiguration;
use Psr\Container\ContainerInterface;

class ConfigurationLocator
{
	public function __construct(private ContainerInterface $locator)
	{
	}

	public function get(string $commandClass): AbstractWebDavConfiguration
	{
		if ($this->locator->has($commandClass))
		{
			$handler = $this->locator->get($commandClass);

			return $handler;
		}
		else
		{
			throw new ConfigrationNotFoundException($commandClass . ' not found');
		}
	}

	public function getProvidedServices()
	{
		return $this->locator->getProvidedServices();
	}
}
