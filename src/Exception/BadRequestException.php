<?php

namespace Eltharin\WebdavBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class BadRequestException extends HttpException
{
	public function __construct(string $message = '')
	{
		parent::__construct(400, $message);
	}
}
