<?php

namespace Eltharin\WebdavBundle\Object;

use DateTimeImmutable;
use Eltharin\WebdavBundle\Interface\DataInfosInterface;

class FileInfos implements DataInfosInterface
{
	private ?string $path;
	private ?string $name;
	private ?string $mimeType;
	private ?string $size;
	private ?DateTimeImmutable $datecreation;
	private ?DateTimeImmutable $datemodif = null;
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

	public function getMimeType(): string
	{
		return $this->mimeType;
	}

	public function setMimeType(string $mimeType): self
	{
		$this->mimeType = $mimeType;

		return $this;
	}

	public function getSize(): string
	{
		return $this->size;
	}

	public function setSize(string $size): self
	{
		$this->size = $size;

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

	public function getDatemodif(): ?DateTimeImmutable
	{
		return $this->datemodif;
	}

	public function setDatemodif(?DateTimeImmutable $datemodif): self
	{
		$this->datemodif = $datemodif;

		return $this;
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

	/* public function getXMLResponse(): string
	{
		return '<response>
			<href>' . str_replace('\\', '/', $this->getPath()) . '</href>
			<propstat>
				<prop>
					<creationdate>' . $this->getDatemodif()->format('Y-m-d\TH:i:s\Z') .  '</creationdate>
					<getcontentlength>' . $this->getSize() . '</getcontentlength>
					<getlastmodified>' . $this->getDatemodif()->format('D, d M Y H:i:s \G\M\T') .  '</getlastmodified>
					<resourcetype>
						<file/>
					</resourcetype>
				</prop>
				<status>HTTP/1.1 200 OK</status>
			</propstat>
			<propstat>
				<prop>
					<displayname/>
					<getcontentlanguage/>
					<getcontenttype/>
					<getetag/>
					<lockdiscovery/>
					<source/>
					<supportedlock/>
				</prop>
				<status>HTTP/1.1 404 Not Found</status>
			</propstat>
		</response>';
	}*/

	public function getXMLResponse(): string
	{
		return '<D:response
xmlns:lp1="DAV:">
<D:href>' . str_replace('\\', '/', $this->getPath()) . '</D:href>
<D:propstat>
<D:prop>
<lp1:resourcetype/>
<lp1:creationdate>' . $this->getDatemodif()->format('Y-m-d\TH:i:s\Z') . /* 2024-03-15T14:56:45Z */ '</lp1:creationdate>
<lp1:getcontentlength>' . $this->getSize() . '</lp1:getcontentlength>
<lp1:getlastmodified>' . $this->getDatemodif()->format('D, d M Y H:i:s \G\M\T') . /* Fri, 15 Mar 2024 14:56:45 GMT */ '</lp1:getlastmodified>
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
<D:getcontenttype>' . $this->mimeType . '</D:getcontenttype>
</D:prop>
<D:status>HTTP/1.1 200 OK</D:status>
</D:propstat>
</D:response>';
	}
}
