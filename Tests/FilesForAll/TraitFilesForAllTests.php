<?php

namespace Eltharin\WebdavBundle\Tests\FilesForAll;

use App\Kernel;
use DateTimeImmutable;
use Eltharin\WebdavBundle\Object\FileInfos;
use Eltharin\WebdavBundle\Object\FolderInfos;
use Eltharin\WebdavBundle\Service\ConfigurationLocator;
use Eltharin\WebdavBundle\Tests\FilesForAll\Data\ConfigurationTest;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Service\ServiceLocatorTrait;
use Symfony\Contracts\Service\ServiceProviderInterface;

trait TraitFilesForAllTests
{
	private string $dir = '';
	protected KernelBrowser $client;
	protected string $racine = '/webdavtest';

	public function setUp(): void
	{
		$class_info = new ReflectionClass($this);
		$dir = dirname($class_info->getFileName());

		(new Filesystem())->remove(dirname($dir, 5) . '/var/cache/test');
		$this->client = static::createClient();

		do
		{
			$this->dir = dirname($dir) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . bin2hex(random_bytes(25));
		}
		while (file_exists($this->dir));

		$fileSystem = new Filesystem();
		$fileSystem->mirror($dir . DIRECTORY_SEPARATOR . 'Data' . DIRECTORY_SEPARATOR . 'arbo', $this->dir);

		$container = static::getContainer();

		$mock = $this->getMockBuilder(ConfigurationLocator::class)
			->setConstructorArgs([
					new class([ConfigurationTest::class => new ConfigurationTest($container->get(Kernel::class), $container->get(RequestStack::class), '')]) implements ServiceProviderInterface {
						use ServiceLocatorTrait;
						private $locator;
					},
				]
			)->getMock();

		$mock->method('getProvidedServices')
			->willReturn([
				ConfigurationTest::class => ConfigurationTest::class,
			]);

		$mock->expects($this->any())->method('get')
			// ->with(ConfigurationLocator::class)
			->willReturn(new ConfigurationTest($this->client->getContainer()->get(Kernel::class), $this->client->getContainer()->get(RequestStack::class), $this->dir));

		$this->client->getContainer()->set(ConfigurationLocator::class, $mock);

		$mockFileInfos = $this->getMockBuilder(FileInfos::class)
			->onlyMethods(['getDatemodif'])
			->getMock();

		$mockFileInfos->method('getDatemodif')
			->willReturn(new DateTimeImmutable('2024-03-18 14:18:23'));

		$fi = new FileInfos();

		$mockFolderInfos = $this->getMockBuilder(FolderInfos::class)->getMock();
		$mockFolderInfos->expects($this->any())->method('getDatecreation')
			->willReturn(new DateTimeImmutable('2024-03-18 14:18:23'));
		$mockFolderInfos->expects($this->any())->method('getDatemodif')
			->willReturn(new DateTimeImmutable('2024-03-19 15:18:23'));
		$this->client->getContainer()->set(FolderInfos::class, $mockFolderInfos);
	}

	public function tearDown(): void
	{
		$fileSystem = new Filesystem();
		$fileSystem->remove($this->dir);

		parent::tearDown();
	}
}
