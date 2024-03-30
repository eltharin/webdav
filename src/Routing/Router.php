<?php

namespace Eltharin\WebdavBundle\Routing;

use Eltharin\WebdavBundle\Controller\WebdavController;
use Eltharin\WebdavBundle\Events\AfterLaunchMethodEvent;
use Eltharin\WebdavBundle\Events\BeforeLaunchMethodEvent;
use Eltharin\WebdavBundle\Service\ConfigurationLocator;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class Router
{
	public function __construct(
		private ContainerInterface $serviceContainer,
		private ConfigurationLocator $locator,
		private RequestStack $requestStack,
		private EventDispatcherInterface $dispatcher,
	) {
	}

	public function __invoke(): Response
	{
		$args = $this->requestStack->getMainRequest()->attributes->get('_route_params');
		$service = $this->locator->get($args['_service']);
		unset($args['_service']);
		$args['path'] ??= '';
		$this->requestStack->getMainRequest()->attributes->set('_route_params', $args);

		$this->dispatcher->dispatch(new BeforeLaunchMethodEvent());

		$controller = $this->serviceContainer->get($service->getController() ?: WebdavController::class);
		$response = $controller->dispatch($service, ...$args);
		$this->dispatcher->dispatch(new AfterLaunchMethodEvent($response));

		return $response;
	}
}
