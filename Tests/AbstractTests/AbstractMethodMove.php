<?php

namespace Eltharin\WebdavBundle\Tests\AbstractTests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractMethodMove extends WebTestCase
{
	protected string $method = 'MOVE';

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

	public function testIfFileExistsWithoutDestination()
	{
		$crawler = $this->client->request($this->method, '/webdavtest/file1.txt');

		$this->assertResponseStatusCodeSame(400);
	}

	public function testIfFOlderExistsWithoutDestination()
	{
		$crawler = $this->client->request($this->method, '/webdavtest/folder1');

		$this->assertResponseStatusCodeSame(400);
	}

	public function testIfFileExists()
	{
		$crawler = $this->client->request($this->method, '/webdavtest/file1.txt', server: ['HTTP_Destination' => '/webdavtest/newfile.txt']);

		$this->assertResponseStatusCodeSame(201);
	}

	public function testIfFOlderExists()
	{
		$crawler = $this->client->request($this->method, '/webdavtest/folder1', server: ['HTTP_Destination' => '/webdavtest/newfolder']);

		$this->assertResponseStatusCodeSame(201);
	}
}
