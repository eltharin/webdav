<?php

namespace Eltharin\WebdavBundle\Security\BasicAuth;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class BasicAuthAuthenticator extends AbstractAuthenticator
{
	public function supports(Request $request): ?bool
	{
		return $request->headers->has('php-auth-user') && $request->headers->has('php-auth-pw');
	}

	public function authenticate(Request $request): Passport
	{
		return new Passport(
			new UserBadge($request->headers->get('php-auth-user')),
			new PasswordCredentials($request->headers->get('php-auth-pw')));
	}

	public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
	{
		return null;
	}

	public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
	{
		return new Response('Bad Authentification', 403);
	}
}
