<?php

namespace Eltharin\WebdavBundle\Tests\AbstractTests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractMethodDelete extends WebTestCase
{
	protected string $method = 'DELETE';

	public function testIfNoPath()
	{
		$crawler = $this->client->request($this->method, '/webdavtest');

		$this->assertResponseStatusCodeSame(400);
	}

	public function testIfFileNotExists()
	{
		$crawler = $this->client->request($this->method, '/webdavtest/filenoexist.txt');

		$this->assertResponseStatusCodeSame(404);
	}

	public function testIfFileExists()
	{
		$crawler = $this->client->request($this->method, '/webdavtest/file1.txt');

		$this->assertResponseStatusCodeSame(204);
		$this->assertEquals('', $this->client->getResponse()->getContent());
	}

	public function testIfFOlderExists()
	{
		$crawler = $this->client->request($this->method, '/webdavtest/folder1');

		$this->assertResponseStatusCodeSame(204);
		$this->assertEquals('', $this->client->getResponse()->getContent());
	}
}
