<?php

namespace Eltharin\WebdavBundle\Interface;

use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

interface SecurityManagerInterface
{
	public function getAuthAuthenticator(): AbstractAuthenticator;

	public function getEntryPoint(): AuthenticationEntryPointInterface;
}
