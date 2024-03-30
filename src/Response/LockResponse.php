<?php

namespace Eltharin\WebdavBundle\Response;

use Eltharin\WebdavBundle\Interface\LockInfosInterface;
use Symfony\Component\HttpFoundation\Response;

class LockResponse extends Response
{
	protected $responses = [];

	public function __construct(?LockInfosInterface $data)
	{
		parent::__construct($this->getXmlData($data->getXMLResponse()), 200, ['Content-Type' => 'text/xml']);
	}

	public function getXmlData($content): string
	{
		$this->content = '<?xml version="1.0" encoding="utf-8"?>
<D:prop xmlns:D="DAV:">' . "\n"
. $content . "\n" .
'</D:prop>';

		return $this->content;
	}
}
