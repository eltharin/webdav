<?php

namespace Eltharin\WebdavBundle\Object;

use DateTimeImmutable;
use Eltharin\WebdavBundle\Interface\LockInfosInterface;

class LockInfos implements LockInfosInterface
{
	public const INFINTE_DATE = '2099-12-31';

	private string $owner = '';
	private DateTimeImmutable $timeOut;
	private string $token = '';

	public function getOwner(): string
	{
		return $this->owner;
	}

	public function setOwner(string $owner): self
	{
		$this->owner = $owner;

		return $this;
	}

	public function getTimeOut(): DateTimeImmutable
	{
		return $this->timeOut;
	}

	public function setTimeOut(DateTimeImmutable $timeOut): self
	{
		$this->timeOut = $timeOut;

		return $this;
	}

	public function getToken(): string
	{
		return $this->token;
	}

	public function setToken(string $token): self
	{
		$this->token = $token;

		return $this;
	}

	public function getXMLResponse(): string
	{
		return '<D:lockdiscovery>
<D:activelock>
<D:locktype>
<D:write/>
</D:locktype>
<D:lockscope>
<D:exclusive/>
</D:lockscope>
<D:depth>infinity</D:depth>
<ns0:owner
xmlns:ns0="DAV:">
<ns0:href>' . $this->owner . '</ns0:href>
</ns0:owner>
<D:timeout>' . ($this->timeOut == new DateTimeImmutable(self::INFINTE_DATE) ? 'Infinite' : 'Second-' . bcsub((new DateTimeImmutable())->format('U'), $this->timeOut->format('U'))) . '</D:timeout>
<D:locktoken>
<D:href>opaquelocktoken:' . $this->token . '</D:href>
</D:locktoken>
</D:activelock>
</D:lockdiscovery>';
	}
}
