<?php

namespace Eltharin\WebdavBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class NotDataInfoException extends HttpException
{
	public function __construct(string $message = '')
	{
		parent::__construct(500, $message);
	}
}
