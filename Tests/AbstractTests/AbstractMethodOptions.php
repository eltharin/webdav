<?php

namespace Eltharin\WebdavBundle\Tests\AbstractTests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractMethodOptions extends WebTestCase
{
	protected string $method = 'OPTIONS';

	public function testOptionIfNoPath()
	{
		$crawler = $this->client->request($this->method, '/webdavtest');

		$this->assertResponseStatusCodeSame(200);
		$this->assertArrayHasKey('allow', $this->client->getResponse()->headers->all());
		$this->assertEquals($this->client->getResponse()->headers->get('allow'), 'OPTIONS, MKCOL, GET, PUT, DELETE, COPY, MOVE, PROPFIND, HEAD, POST, TRACE, LOCK, UNLOCK'); /* PROPPATCH, , ORDERPATCH */
		$this->assertEquals($this->client->getResponse()->getContent(), '');
	}

	public function testOptionIfFileNotExists()
	{
		$crawler = $this->client->request($this->method, '/webdavtest/filenoexist.txt');

		$this->assertResponseStatusCodeSame(200);
		$this->assertArrayHasKey('allow', $this->client->getResponse()->headers->all());
		$this->assertEquals($this->client->getResponse()->headers->get('allow'), 'OPTIONS, MKCOL, GET, PUT, DELETE, COPY, MOVE, PROPFIND, HEAD, POST, TRACE, LOCK, UNLOCK'); /* PROPPATCH, , ORDERPATCH */
		$this->assertEquals($this->client->getResponse()->getContent(), '');
	}

	public function testOptionIfFileExists()
	{
		$crawler = $this->client->request($this->method, '/webdavtest/file1.txt');

		$this->assertResponseStatusCodeSame(200);
		$this->assertArrayHasKey('allow', $this->client->getResponse()->headers->all());
		$this->assertEquals($this->client->getResponse()->headers->get('allow'), 'OPTIONS, MKCOL, GET, PUT, DELETE, COPY, MOVE, PROPFIND, HEAD, POST, TRACE, LOCK, UNLOCK'); /* PROPPATCH, , ORDERPATCH */
		$this->assertEquals($this->client->getResponse()->getContent(), '');
	}

	public function testOptionIfFOlderExists()
	{
		$crawler = $this->client->request($this->method, '/webdavtest/folder1');

		$this->assertResponseStatusCodeSame(200);
		$this->assertArrayHasKey('allow', $this->client->getResponse()->headers->all());
		$this->assertEquals($this->client->getResponse()->headers->get('allow'), 'OPTIONS, MKCOL, GET, PUT, DELETE, COPY, MOVE, PROPFIND, HEAD, POST, TRACE, LOCK, UNLOCK'); /* PROPPATCH, , ORDERPATCH */
		$this->assertEquals($this->client->getResponse()->getContent(), '');
	}
}
