<?php

namespace Eltharin\WebdavBundle\Tests\FilesForAll;

use Eltharin\WebdavBundle\Tests\AbstractTests\AbstractMethodMkcol;

class MethodMkcolTest extends AbstractMethodMkcol
{
	use TraitFilesForAllTests;

	public function testIfFileNotExists2()
	{
		$this->assertFileDoesNotExist($this->dir . DIRECTORY_SEPARATOR . 'foldernoexist');
		$crawler = $this->client->request($this->method, '/webdavtest/foldernoexist');

		$this->assertResponseStatusCodeSame(201);
		$this->assertFileExists($this->dir . DIRECTORY_SEPARATOR . 'foldernoexist');
	}
}
