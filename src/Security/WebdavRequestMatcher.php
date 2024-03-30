<?php

namespace Eltharin\WebdavBundle\Security;

use Eltharin\WebdavBundle\Service\ConfigurationLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;

class WebdavRequestMatcher implements RequestMatcherInterface
{
	public function __construct(private ConfigurationLocator $locator)
	{
	}

	public function matches(Request $request): bool
	{
		if (($request->getMethod() == 'OPTIONS' || $request->getMethod() == 'PROPFIND') && str_starts_with($request->getPathInfo(), '/'))
		{
			return true;
		}

		foreach ($this->locator->getProvidedServices() as $serviceName)
		{
			$service = $this->locator->get($serviceName);
			if (str_starts_with($request->getPathInfo(), $service->getRouteData()->getPath()) && in_array($request->getMethod(), $service->getFileManager()->getMethods()))
			{
				return true;
			}
		}

		return false;
	}
}
