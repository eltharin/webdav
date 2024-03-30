<?php

namespace Eltharin\WebdavBundle\Tests\FilesForAll;

use Eltharin\WebdavBundle\Tests\AbstractTests\AbstractMethodUnlock;

class MethodUnlockTest extends AbstractMethodUnlock
{
	use TraitFilesForAllTests {TraitFilesForAllTests::setUp as parentSetup; }

	public function setUp(): void
	{
		$this->parentSetup();
		file_put_contents($this->dir . DIRECTORY_SEPARATOR . 'file1.txt~.lock', '{"owner":"XXX","token":"12345678-1234-5678-abcd-abcdef012345","timeout":"2098-03-29 10:37:18"}');
		file_put_contents($this->dir . DIRECTORY_SEPARATOR . 'folder1~.lock', '{"owner":"XXX","token":"12345678-1234-5678-abcd-abcdef012345","timeout":"2098-03-29 10:37:18"}');
	}

	public function testIfFileExists2()
	{
		$crawler = $this->client->request($this->method, '/webdavtest/file1.txt', server: ['HTTP_lock-Token' => '<opaquelocktoken:12345678-1234-5678-abcd-abcdef012345>']);

		$this->assertFileDoesNotExist($this->dir . DIRECTORY_SEPARATOR . 'file1.txt~.lock');
	}

	public function testIfFOlderExists2()
	{
		$crawler = $this->client->request($this->method, '/webdavtest/folder1', server: ['HTTP_lock-Token' => '<opaquelocktoken:12345678-1234-5678-abcd-abcdef012345>']);

		$this->assertFileDoesNotExist($this->dir . DIRECTORY_SEPARATOR . 'folder1~.lock');
	}
}
