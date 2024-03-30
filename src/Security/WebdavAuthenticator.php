<?php

namespace Eltharin\WebdavBundle\Security;

use Eltharin\WebdavBundle\Service\ConfigurationLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class WebdavAuthenticator extends AbstractAuthenticator
{
	public function __construct(private ConfigurationLocator $locator)
	{
	}

	public function supports(Request $request): ?bool
	{
		if ($request->attributes->get('_service') == '' || $this->locator->get($request->attributes->get('_service'))?->getSecurityManager() === null)
		{
			return false;
		}

		return $this->locator->get($request->attributes->get('_service'))->getSecurityManager()->getAuthAuthenticator()->supports($request);
	}

	public function authenticate(Request $request): Passport
	{
		return $this->locator->get($request->attributes->get('_service'))->getSecurityManager()->getAuthAuthenticator()->authenticate($request);
	}

	public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
	{
		return $this->locator->get($request->attributes->get('_service'))->getSecurityManager()->getAuthAuthenticator()->onAuthenticationSuccess($request, $token, $firewallName);
	}

	public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
	{
		return $this->locator->get($request->attributes->get('_service'))->getSecurityManager()->getAuthAuthenticator()->onAuthenticationFailure($request, $exception);
	}
}
