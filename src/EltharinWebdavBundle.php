<?php

namespace Eltharin\WebdavBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class EltharinInvitationsBundle extends AbstractBundle
{
	public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
	{
		
	}
	
	public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
	{
		
	}
}