<?php

namespace Eltharin\WebdavBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class MethodNotFoundException extends HttpException
{
	public function __construct(string $method = '')
	{
		parent::__construct(404, $method . ' is not found');
	}
}
