<?php

namespace Eltharin\WebdavBundle\Security\BasicAuth;

use Eltharin\WebdavBundle\Interface\SecurityManagerInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class BasicAuthManager implements SecurityManagerInterface
{
	public function __construct(private BasicAuthAuthenticator $authAuthenticator, private BasicAuthAuthenticationEntryPoint $entryPoint)
	{
	}

	public function getAuthAuthenticator(): AbstractAuthenticator
	{
		return $this->authAuthenticator;
	}

	public function getEntryPoint(): AuthenticationEntryPointInterface
	{
		return $this->entryPoint;
	}
}
