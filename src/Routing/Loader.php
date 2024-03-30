<?php

namespace Eltharin\WebdavBundle\Routing;

use Eltharin\WebdavBundle\Service\ConfigurationLocator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Loader\ContainerLoader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class Loader
{
	public function __construct(
		private ConfigurationLocator $locator,
		private RequestStack $requestStack,
		private EventDispatcherInterface $dispatcher,
	) {
	}

	public function __invoke(ContainerLoader $routes)
	{
		$collection = new RouteCollection();

		foreach ($this->locator->getProvidedServices() as $serviceName)
		{
			$service = $this->locator->get($serviceName);
			$methods = $service->getFileManager()->getMethods();

			$collection->add($service->getRouteName() . '_wop',
				(new Route($service->getRouteData()->getPath()))
					->setRequirements($service->getRouteData()->getRequirements())
					->setDefaults(['_controller' => Router::class, '_service' => $serviceName])
					->setMethods($methods));

			$collection->add($service->getRouteName() . '_wp',
				(new Route($service->getRouteData()->getPath() . '/{path}'))
					->setRequirements(array_merge($service->getRouteData()->getRequirements(), ['path' => '.*']))
					->setDefaults(['_controller' => Router::class, '_service' => $serviceName])
					->setMethods($methods));

			$collection->add($service->getRouteName() . '_optionsroot',
				(new Route('/'))
					->setDefaults(['_controller' => Router::class, '_service' => $serviceName])
					->setMethods(['OPTIONS', 'PROPFIND']),
				-10
			);
		}

		return $collection;
	}
}
