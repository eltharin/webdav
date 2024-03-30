<?php

namespace Eltharin\WebdavBundle\Tests\AbstractTests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractMethodPut extends WebTestCase
{
	protected string $method = 'PUT';

	public function testIfNoPath()
	{
		$crawler = $this->client->request($this->method, '/webdavtest', content: 'file content');

		$this->assertResponseStatusCodeSame(409);
	}

	public function testIfFileNotExists()
	{
		$crawler = $this->client->request($this->method, '/webdavtest/filenoexist.txt', content: 'file content');

		$this->assertResponseStatusCodeSame(201);
	}

	public function testIfFileExists()
	{
		$crawler = $this->client->request($this->method, '/webdavtest/file1.txt', content: 'file new content');

		$this->assertResponseStatusCodeSame(204);
		$this->assertEquals('', $this->client->getResponse()->getContent());
	}

	public function testIfFOlderExists()
	{
		$crawler = $this->client->request($this->method, '/webdavtest/folder1', content: 'file content');

		$this->assertResponseStatusCodeSame(409);
	}
}
