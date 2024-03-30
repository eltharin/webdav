<?php

namespace Eltharin\WebdavBundle\Tests\AbstractTests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractMethodProppatch extends WebTestCase
{
	protected string $method = 'PROPATCH';

	public function testIfNoPath()
	{
		$crawler = $this->client->request($this->method, '/webdavtest');

		$this->assertResponseStatusCodeSame(418);
	}

	public function testIfFileNotExists()
	{
		$crawler = $this->client->request($this->method, '/webdavtest/filenoexist.txt');

		$this->assertResponseStatusCodeSame(418);
	}

	public function testIfFileExists()
	{
		$crawler = $this->client->request($this->method, '/webdavtest/file1.txt');

		$this->assertResponseStatusCodeSame(418);
		$this->assertEquals('', $this->client->getResponse()->getContent());
	}

	public function testIfFOlderExists()
	{
		$crawler = $this->client->request($this->method, '/webdavtest/folder1');

		$this->assertResponseStatusCodeSame(418);
		$this->assertEquals('', $this->client->getResponse()->getContent());
	}
}
