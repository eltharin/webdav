<?php

namespace Eltharin\WebdavBundle\Security;

use Eltharin\WebdavBundle\Service\ConfigurationLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class WebdavAuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
	public function __construct(private ConfigurationLocator $locator)
	{
	}

	public function start(Request $request, ?AuthenticationException $authException = null): Response
	{
		return $this->locator->get($request->attributes->get('_service'))->getSecurityManager()->getEntryPoint()->start($request, $authException);
	}
}
