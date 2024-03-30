<?php

namespace Eltharin\WebdavBundle\Events;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

class AfterLaunchMethodEvent extends Event
{
	public function __construct(private Response $response)
	{
	}

	public function getResponse(): Response
	{
		return $this->response;
	}
}
