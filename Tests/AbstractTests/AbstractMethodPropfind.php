<?php

namespace Eltharin\WebdavBundle\Tests\AbstractTests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractMethodPropfind extends WebTestCase
{
	protected string $method = 'PROPFIND';

	public function getXmlResponseToObject($xml)
	{
		return json_decode(json_encode(simplexml_load_string($xml, null, 0, 'D', true)), true)['response'];
	}

	public function testIfNoPath()
	{
		$crawler = $this->client->request($this->method, $this->racine);

		$this->assertResponseStatusCodeSame(207);

		$response = $this->getXmlResponseToObject($this->client->getResponse()->getContent());

		$this->assertEquals(5, count($response));
		$this->assertEquals($this->racine . '/', $response[0]['href']);
		$this->assertEquals($this->racine . '/file1.txt', $response[1]['href']);
		$this->assertEquals($this->racine . '/file2.log', $response[2]['href']);
		$this->assertEquals($this->racine . '/folder1/', $response[3]['href']);
		$this->assertEquals($this->racine . '/folder2/', $response[4]['href']);
	}

	public function testIfFileNotExists()
	{
		$crawler = $this->client->request($this->method, $this->racine . '/filenoexist.txt');

		$this->assertResponseStatusCodeSame(404);
	}

	public function testIfFileExists()
	{
		$crawler = $this->client->request($this->method, $this->racine . '/file1.txt');

		$this->assertResponseStatusCodeSame(207);

		$response = $this->getXmlResponseToObject($this->client->getResponse()->getContent());

		$this->assertEquals(2, count($response));
		$this->assertEquals($this->racine . '/file1.txt', $response['href']);
		$this->assertEquals(2, count($response['propstat']));
	}

	public function testIfFOlderExists()
	{
		$crawler = $this->client->request($this->method, $this->racine . '/folder1', server: ['HTTP_Depth' => 1]);

		$this->assertResponseStatusCodeSame(207);

		$response = $this->getXmlResponseToObject($this->client->getResponse()->getContent());

		$this->assertEquals(3, count($response));
		$this->assertEquals($this->racine . '/folder1/', $response[0]['href']);
		$this->assertEquals($this->racine . '/folder1/file3.txt', $response[1]['href']);
		$this->assertEquals($this->racine . '/folder1/folder3/', $response[2]['href']);
	}

	public function testIfFOlderExistswithNoDepth()
	{
		$crawler = $this->client->request($this->method, $this->racine . '/folder1', server: ['HTTP_Depth' => 0]);

		$this->assertResponseStatusCodeSame(207);

		$response = $this->getXmlResponseToObject($this->client->getResponse()->getContent());

		$this->assertEquals(2, count($response));
		$this->assertEquals($this->racine . '/folder1/', $response['href']);
		$this->assertEquals(2, count($response['propstat']));
	}
}
