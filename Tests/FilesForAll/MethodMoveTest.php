<?php

namespace Eltharin\WebdavBundle\Tests\FilesForAll;

use Eltharin\WebdavBundle\Tests\AbstractTests\AbstractMethodMove;

class MethodMoveTest extends AbstractMethodMove
{
	use TraitFilesForAllTests;

	public function testIfFileExists2()
	{
		$this->assertFileDoesNotExist($this->dir . DIRECTORY_SEPARATOR . 'newfile.txt');
		$this->assertFileExists($this->dir . DIRECTORY_SEPARATOR . 'file1.txt');

		$crawler = $this->client->request($this->method, '/webdavtest/file1.txt', server: ['HTTP_Destination' => '/webdavtest/newfile.txt']);

		$this->assertResponseStatusCodeSame(201);
		$this->assertEquals('', $this->client->getResponse()->getContent());

		$this->assertFileDoesNotExist($this->dir . DIRECTORY_SEPARATOR . 'file1.txt');
		$this->assertFileExists($this->dir . DIRECTORY_SEPARATOR . 'newfile.txt');
	}

	public function testIfFOlderExists2()
	{
		$this->assertFileDoesNotExist($this->dir . DIRECTORY_SEPARATOR . 'newfolder');
		$this->assertFileExists($this->dir . DIRECTORY_SEPARATOR . 'folder1');

		$crawler = $this->client->request($this->method, '/webdavtest/folder1', server: ['HTTP_Destination' => '/webdavtest/newfolder']);

		$this->assertResponseStatusCodeSame(201);
		$this->assertEquals('', $this->client->getResponse()->getContent());

		$this->assertFileDoesNotExist($this->dir . DIRECTORY_SEPARATOR . 'folder1');
		$this->assertFileExists($this->dir . DIRECTORY_SEPARATOR . 'newfolder');
	}
}
