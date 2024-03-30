<?php

namespace Eltharin\WebdavBundle\Tests\AbstractTests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractMethodMkcol extends WebTestCase
{
	protected string $method = 'MKCOL';

	public function testIfNoPath()
	{
		$crawler = $this->client->request($this->method, '/webdavtest');

		$this->assertResponseStatusCodeSame(405);
	}

	public function testIfFileNotExists()
	{
		$crawler = $this->client->request($this->method, '/webdavtest/foldernoexist');

		$this->assertResponseStatusCodeSame(201);
	}

	public function testIfFileExists()
	{
		$crawler = $this->client->request($this->method, '/webdavtest/file1.txt');

		$this->assertResponseStatusCodeSame(405);
	}

	public function testIfFOlderExists()
	{
		$crawler = $this->client->request($this->method, '/webdavtest/folder1');

		$this->assertResponseStatusCodeSame(405);
	}
}
