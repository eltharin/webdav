<?php

namespace Eltharin\WebdavBundle\Routing;

class RouteData
{
	public function __construct(private string $path, private array $requirements = [])
	{
	}

	public function getPath(): string
	{
		return $this->path;
	}

	public function getRequirements(): array
	{
		return $this->requirements;
	}
}
