<?php

namespace Eltharin\WebdavBundle\Object;

use DateTimeImmutable;
use Eltharin\WebdavBundle\Exception\NotDataInfoException;
use Eltharin\WebdavBundle\Interface\DataInfosInterface;

class FolderInfos implements DataInfosInterface
{
	private string $path;
	private string $name;
	private array $ressource = [];
	private DateTimeImmutable $datecreation;
	private DateTimeImmutable $datemodif;
	private ?LockInfos $lockInfos = null;

	public function getPath(): string
	{
		return $this->path;
	}

	public function setPath(string $path): self
	{
		$this->path = $path;

		return $this;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function setName(string $name): self
	{
		$this->name = $name;

		return $this;
	}

	public function getDatecreation(): DateTimeImmutable
	{
		return $this->datecreation;
	}

	public function setDatecreation(DateTimeImmutable $datecreation): self
	{
		$this->datecreation = $datecreation;

		return $this;
	}

	public function getDatemodif(): DateTimeImmutable
	{
		return $this->datemodif;
	}

	public function setDatemodif(DateTimeImmutable $datemodif): self
	{
		$this->datemodif = $datemodif;

		return $this;
	}

	public function addRessource(FolderInfos|FileInfos $ressource)
	{
		$this->ressource[] = $ressource;
	}

	public function getRessource()
	{
		return $this->ressource;
	}

	public function getLockInfos(): ?LockInfos
	{
		return $this->lockInfos;
	}

	public function setLockInfos(?LockInfos $lockInfos): self
	{
		$this->lockInfos = $lockInfos;

		return $this;
	}

	public function getXMLResponse(): string
	{
		$response = '<D:response xmlns:lp1="DAV:">
<D:href>' . str_replace('\\', '/', $this->getPath()) . '/</D:href>
<D:propstat>
<D:prop>
<lp1:resourcetype>
<D:collection/>
</lp1:resourcetype>
<lp1:creationdate>' . $this->getDatemodif()->format('Y-m-d\TH:i:s\Z') . /* 2024-03-15T14:56:45Z */ '</lp1:creationdate>
<lp1:getlastmodified>' . $this->getDatemodif()->format('D, d M Y H:i:s e') . /* Sat, 16 Mar 2024 12:23:46 GMT */ '</lp1:getlastmodified>
<D:supportedlock>
<D:lockentry>
<D:lockscope>
<D:exclusive/>
</D:lockscope>
<D:locktype>
<D:write/>
</D:locktype>
</D:lockentry>
<D:lockentry>
<D:lockscope>
<D:shared/>
</D:lockscope>
<D:locktype>
<D:write/>
</D:locktype>
</D:lockentry>
</D:supportedlock>
' . ($this->lockInfos != null ? $this->lockInfos->getXMLResponse() : '<D:lockdiscovery/>') . '
<D:getcontenttype>httpd/unix-directory</D:getcontenttype>
</D:prop>
<D:status>HTTP/1.1 200 OK</D:status>
</D:propstat>
</D:response>';

		foreach ($this->getRessource() as $ressource)
		{
			if (!is_a($ressource, DataInfosInterface::class))
			{
				throw new NotDataInfoException('not good type');
			}

			$response .= $ressource->getXMLResponse();
		}

		return $response;
	}
}
