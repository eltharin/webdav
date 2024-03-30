<?php

namespace Eltharin\WebdavBundle\Response;

use Eltharin\WebdavBundle\Interface\DataInfosInterface;
use Symfony\Component\HttpFoundation\Response;

class MultiStatusResponse extends Response
{
	protected $responses = [];

	public function __construct(?DataInfosInterface $data)
	{
		$xmlData = $this->getXmlData($data->getXMLResponse());
		parent::__construct($xmlData, 207, ['Content-Type' => 'text/xml', 'Content-Length00' => mb_strlen($xmlData)]);
	}

	public function getXmlData($content): string
	{
		$this->content = '<?xml version="1.0" encoding="utf-8"?>
<D:multistatus xmlns:D="DAV:">' . "\n"
. $content . "\n" .
'</D:multistatus>
';

		return $this->content;
	}
}
