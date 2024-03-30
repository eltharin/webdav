<?php

namespace Eltharin\WebdavBundle;

use Eltharin\WebdavBundle\Controller\WebdavController;
use Eltharin\WebdavBundle\Interface\AbstractWebDavConfiguration;
use Eltharin\WebdavBundle\Routing\Loader;
use Eltharin\WebdavBundle\Routing\Router;
use Eltharin\WebdavBundle\Security\BasicAuth\BasicAuthAuthenticationEntryPoint;
use Eltharin\WebdavBundle\Security\BasicAuth\BasicAuthAuthenticator;
use Eltharin\WebdavBundle\Security\BasicAuth\BasicAuthManager;
use Eltharin\WebdavBundle\Security\WebdavAuthenticationEntryPoint;
use Eltharin\WebdavBundle\Security\WebdavAuthenticator;
use Eltharin\WebdavBundle\Security\WebdavRequestMatcher;
use Eltharin\WebdavBundle\Service\ConfigurationLocator;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_locator;

class EltharinWebdavBundle extends AbstractBundle
{
	public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
	{
	}

	public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
	{
		$builder->registerForAutoconfiguration(AbstractWebDavConfiguration::class)
			->addTag(AbstractWebDavConfiguration::class)
		;

		$container->services()
			->set(ConfigurationLocator::class)
			->args([
				tagged_locator(AbstractWebDavConfiguration::class, 'key'),
			])
		;

		$container->services()
			->set(Loader::class)
			->args([
				service(ConfigurationLocator::class),
				service('request_stack'),
				service(EventDispatcherInterface::class),
			])
			->tag('routing.route_loader')
		;

		$container->services()
			->set(Router::class)
			->args([
				service('service_container'),
				service(ConfigurationLocator::class),
				service('request_stack'),
				service(EventDispatcherInterface::class),
			])
			->tag('controller.service_arguments')
		;

		$container->services()
			->set(WebdavController::class)
			->args([
				service('request_stack'),
				service('security.authorization_checker'),
				service(HttpClientInterface::class),
			])
			->tag('controller.service_arguments')
		;

		$container->services()->set(WebdavRequestMatcher::class)->args([service(ConfigurationLocator::class)]);

		$container->services()->set(WebdavAuthenticator::class)->args([service(ConfigurationLocator::class)]);
		$container->services()->set(WebdavAuthenticationEntryPoint::class)->args([service(ConfigurationLocator::class)]);

		$container->services()->set(BasicAuthAuthenticator::class);
		$container->services()->set(BasicAuthAuthenticationEntryPoint::class);
		$container->services()->set(BasicAuthManager::class)->args([service(BasicAuthAuthenticator::class), service(BasicAuthAuthenticationEntryPoint::class)]);
	}
}
