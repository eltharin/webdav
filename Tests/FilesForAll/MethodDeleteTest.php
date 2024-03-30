<?php

namespace Eltharin\WebdavBundle\Tests\FilesForAll;

use Eltharin\WebdavBundle\Tests\AbstractTests\AbstractMethodDelete;

class MethodDeleteTest extends AbstractMethodDelete
{
	use TraitFilesForAllTests;

	public function testIfFileExists2()
	{
		$this->assertFileExists($this->dir . DIRECTORY_SEPARATOR . 'file1.txt');
		$crawler = $this->client->request($this->method, '/webdavtest/file1.txt');

		$this->assertResponseStatusCodeSame(204);
		$this->assertFileDoesNotExist($this->dir . DIRECTORY_SEPARATOR . 'file1.txt');
	}

	public function testIfFOlderExists2()
	{
		$this->assertFileExists($this->dir . DIRECTORY_SEPARATOR . 'folder1');
		$crawler = $this->client->request($this->method, '/webdavtest/folder1');

		$this->assertResponseStatusCodeSame(204);
		$this->assertFileDoesNotExist($this->dir . DIRECTORY_SEPARATOR . 'folder1');
	}
}
