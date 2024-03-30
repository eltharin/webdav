<?php

namespace Eltharin\WebdavBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class NoAutorizationException extends HttpException
{
	public function __construct(string $message = '')
	{
		parent::__construct(403, $message);
	}
}
