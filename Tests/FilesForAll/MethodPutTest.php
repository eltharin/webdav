<?php

namespace Eltharin\WebdavBundle\Tests\FilesForAll;

use Eltharin\WebdavBundle\Tests\AbstractTests\AbstractMethodPut;

class MethodPutTest extends AbstractMethodPut
{
	use TraitFilesForAllTests;

	public function testIfFileNotExistsEmptyContent2()
	{
		$crawler = $this->client->request($this->method, '/webdavtest/filenoexist.txt');

		$this->assertFileExists($this->dir . DIRECTORY_SEPARATOR . 'filenoexist.txt');
		$this->assertEquals('', file_get_contents($this->dir . DIRECTORY_SEPARATOR . 'filenoexist.txt'));
	}

	public function testIfFileNotExists2()
	{
		$crawler = $this->client->request($this->method, '/webdavtest/filenoexist.txt', content: 'file content');

		$this->assertFileExists($this->dir . DIRECTORY_SEPARATOR . 'filenoexist.txt');
		$this->assertEquals('file content', file_get_contents($this->dir . DIRECTORY_SEPARATOR . 'filenoexist.txt'));
	}

	public function testIfFileExists2()
	{
		$crawler = $this->client->request($this->method, '/webdavtest/file1.txt', content: 'file new content');

		$this->assertFileExists($this->dir . DIRECTORY_SEPARATOR . 'file1.txt');
		$this->assertEquals('file new content', file_get_contents($this->dir . DIRECTORY_SEPARATOR . 'file1.txt'));
	}
}
