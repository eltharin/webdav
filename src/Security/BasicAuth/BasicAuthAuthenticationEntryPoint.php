<?php

namespace Eltharin\WebdavBundle\Security\BasicAuth;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class BasicAuthAuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
	public function start(Request $request, ?AuthenticationException $authException = null): Response
	{
		return new Response('No authorisation to Webdav', 401, ['WWW-Authenticate' => 'Basic realm="these words are not used", charset="UTF-8"']);
	}
}
