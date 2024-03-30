<?php

namespace Eltharin\WebdavBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class FileLockedException extends HttpException
{
	public function __construct(string $method = '')
	{
		parent::__construct(423, $method . ' locked');
	}
}
