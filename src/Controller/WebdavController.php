<?php

namespace Eltharin\WebdavBundle\Controller;

use Eltharin\WebdavBundle\Exception\BadRequestException;
use Eltharin\WebdavBundle\Exception\MethodNotFoundException;
use Eltharin\WebdavBundle\Interface\FileManagerInterface;
use Eltharin\WebdavBundle\Response\LockResponse;
use Eltharin\WebdavBundle\Response\MultiStatusResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class WebdavController
{
	protected FileManagerInterface $fileManager;

	public function __construct(protected RequestStack $requestStack, protected AuthorizationCheckerInterface $security)
	{
	}

	public function dispatch($service, $path): Response
	{
		$this->fileManager = $service->getFileManager();

		$method = strtolower($this->requestStack->getMainRequest()?->getMethod());
		$args2 = $this->requestStack->getMainRequest()?->attributes->get('_route_params');

		$checkFileType = $this->fileManager->checkFileType($path);

		$this->fileManager->checkAutorization($path, $this->security);

		if ($checkFileType == null)
		{
			return $this->{'method_' . $method . '_unexist'}($path);
		}
		elseif ($checkFileType == FileManagerInterface::TYPE_FILE)
		{
			return $this->{'method_' . $method . '_file'}($path);
		}
		elseif ($checkFileType == FileManagerInterface::TYPE_FOLDER)
		{
			return $this->{'method_' . $method . '_folder'}($path);
		}
		else
		{
			return new Response('evenement non géré', 500);
		}
	}

	public function method_options_unexist($path): Response
	{
		return new Response('', 200, [
			'Allow' => [implode(', ', $this->fileManager->getMethods())],
			'DAV' => '1, 2, <http://apache.org/dav/propset/fs/1>',
			'MS-Author-Via' => 'DAV',
			'Content-Length' => 0,
			'Content-Type' => 'text/plain',
		]);
	}

	public function method_options_file($path): Response
	{
		return $this->method_options_unexist($path);
	}

	public function method_options_folder($path): Response
	{
		return $this->method_options_unexist($path);
	}

	public function method_mkcol_unexist($path): Response
	{
		if ($this->fileManager->createDirectory($path))
		{
			return new Response('OK', 201);
		}

		throw new BadRequestException();

		return new Response('', 400);
	}

	public function method_get_file($path): Response
	{
		$fileContent = $this->fileManager->getFileContent($path);

		return new Response($fileContent, 200, ['Content-Length' => mb_strlen($fileContent)]);
	}

	public function method_get_folder($path): Response
	{
		return new Response('', 200);
	}

	public function method_put_unexist($path): Response
	{
		if ($this->fileManager->appendFile($path, $this->requestStack->getMainRequest()->getContent()))
		{
			return new Response('', 201);
		}

		throw new BadRequestException();

		return new Response('', 400);
	}

	public function method_put_file($path): Response
	{
		if ($this->fileManager->appendFile($path, $this->requestStack->getMainRequest()->getContent()))
		{
			return new Response('', 204);
		}

		throw new BadRequestException();

		return new Response('', 400);
	}

	public function method_delete_file($path): Response
	{
		if ($this->fileManager->deleteFile($path))
		{
			return new Response('', 204);
		}

		throw new BadRequestException();

		return new Response('', 400);
	}

	public function method_delete_folder($path): Response
	{
		if ($path == '')
		{
			throw new BadRequestException('Can\'t delete root folder');
		}

		if ($this->fileManager->deleteDirectory($path))
		{
			return new Response('', 204);
		}

		throw new BadRequestException();

		return new Response('', 400);
	}

	public function method_lock_file($path): Response
	{
		if (!$this->requestStack->getMainRequest()->headers->has('timeout'))
		{
			throw new BadRequestException('Header timeout not set');
		}

		$body = $this->requestStack->getMainRequest()->getContent();
		$data = json_decode(json_encode(simplexml_load_string($body, null, 0, 'D', true)));

		if ($lockData = $this->fileManager->lockFile($path, $data->owner->href ?? 'XXX', $this->requestStack->getMainRequest()->headers->get('timeout')))
		{
			return new LockResponse($lockData);
		}

		throw new BadRequestException();

		return new Response('', 400);
	}

	public function method_lock_folder($path): Response
	{
		if (!$this->requestStack->getMainRequest()->headers->has('timeout'))
		{
			throw new BadRequestException('Header timeout not set');
		}

		if ($path == '')
		{
			throw new BadRequestException('Can\'t lock root folder');
		}

		$body = $this->requestStack->getMainRequest()->getContent();
		$data = json_decode(json_encode(simplexml_load_string($body, null, 0, 'D', true)));

		if ($lockData = $this->fileManager->lockFolder($path, $data->owner->href ?? 'XXX', $this->requestStack->getMainRequest()->headers->get('timeout')))
		{
			return new LockResponse($lockData);
		}

		throw new BadRequestException();

		return new Response('', 400);
	}

	public function method_unlock_file($path): Response
	{
		if (!$this->requestStack->getMainRequest()->headers->has('lock-token'))
		{
			throw new BadRequestException('Header lock-token not set');
		}

		if ($this->fileManager->unlockFile($path, $this->requestStack->getMainRequest()->headers->get('lock-token')))
		{
			return new Response('', 204);
		}

		throw new BadRequestException('Can\'t unlock file');
	}

	public function method_unlock_folder($path): Response
	{
		if (!$this->requestStack->getMainRequest()->headers->has('lock-token'))
		{
			throw new BadRequestException('Header lock-token not set');
		}

		if ($path == '')
		{
			throw new BadRequestException('Can\'t unlock root folder');
		}

		if ($this->fileManager->unlockFolder($path, $this->requestStack->getMainRequest()->headers->get('lock-token')))
		{
			return new Response('', 204);
		}

		throw new BadRequestException('Can\'t unlock folder');
	}

	public function method_propfind_file($path): Response
	{
		return new MultiStatusResponse($this->fileManager->getFileInfos($path));
	}

	public function method_propfind_folder($path): Response
	{
		return new MultiStatusResponse($this->fileManager->getFolderInfos($path, $this->requestStack->getMainRequest()->headers->get('Depth') ?? 1));
	}

	public function method_move_folder($path): Response
	{
		if ($path == '')
		{
			throw new BadRequestException('Can\'t move root folder');
		}

		if (!$this->requestStack->getMainRequest()->headers->has('destination'))
		{
			throw new BadRequestException('Header Destination not set');
		}

		if ($this->fileManager->moveDirectory($path, $this->requestStack->getMainRequest()->headers->get('destination')))
		{
			return new Response('', 201);
		}

		throw new BadRequestException();

		return new Response('', 400);
	}

	public function method_move_file($path): Response
	{
		if (!$this->requestStack->getMainRequest()->headers->has('destination'))
		{
			throw new BadRequestException('Header Destination not set');
		}

		if ($this->fileManager->moveFile($path, $this->requestStack->getMainRequest()->headers->get('destination')))
		{
			return new Response('', 201);
		}

		throw new BadRequestException();

		return new Response('', 400);
	}

	// -- static responses

	public function method_get_unexist($path): Response
	{
		return new Response($path . ' not exists', 404);
	}

	public function method_delete_unexist($path): Response
	{
		return new Response($path . ' not exists', 404);
	}

	public function method_propfind_unexist($path): Response
	{
		return new Response($path . ' not exists', 404);
	}

	public function method_lock_unexist($path): Response
	{
		throw new BadRequestException();
	}

	public function method_unlock_unexist($path): Response
	{
		return new Response('evenement non géré', 400);
	}

	public function method_mkcol_file($path): Response
	{
		return new Response('Can not make MKCOL on a file', 405);
	}

	public function method_mkcol_folder($path): Response
	{
		return new Response('folder exists', 405);
	}

	public function method_put_folder($path): Response
	{
		return new Response('Cannot PUT to a collection', 409);
	}

	public function method_head_unexist($path): Response
	{
		return new Response('', 404);
	}

	public function method_head_file($path): Response
	{
		return new Response('', 200);
	}

	public function method_head_folder($path): Response
	{
		return new Response('', 200);
	}

	public function method_move_unexist($path): Response
	{
		return new Response($path . ' not exists', 404);
	}

	// ------------------------------------------------

	public function method_copy_unexist($path): Response
	{
		throw new MethodNotFoundException('method_copy');

		return new Response();
	}

	public function method_copy_file($path): Response
	{
		throw new MethodNotFoundException('method_copy');

		return new Response();
	}

	public function method_copy_folder($path): Response
	{
		throw new MethodNotFoundException('method_copy');

		return new Response();
	}

	public function method_proppatch_unexist($path): Response
	{
		throw new MethodNotFoundException('method_proppatch');

		return new Response('evenement non géré', 200);
	}

	public function method_proppatch_file($path): Response
	{
		throw new MethodNotFoundException('method_proppatch');

		return new Response('evenement non géré', 200);
	}

	public function method_proppatch_folder($path): Response
	{
		throw new MethodNotFoundException('method_proppatch');

		return new Response('evenement non géré', 200);
	}

	/*public function method_post_unexist($path): Response
	{
		throw new MethodNotFoundException('method_post');

		return new Response();
	}

	public function method_post_file($path): Response
	{
		throw new MethodNotFoundException('method_post');

		return new Response();
	}

	public function method_post_folder($path): Response
	{
		throw new MethodNotFoundException('method_post');

		return new Response();
	}*/

	public function method_trace_unexist($path): Response
	{
		throw new MethodNotFoundException('method_trace');

		return new Response();
	}

	public function method_trace_file($path): Response
	{
		// throw new MethodNotFoundException('method_trace');

		return new Response();
	}

	public function method_trace_folder($path): Response
	{
		throw new MethodNotFoundException('method_trace');

		return new Response();
	}

	public function method_orderpatch_unexist($path): Response
	{
		throw new MethodNotFoundException('method_orderpatch');

		return new Response();
	}

	public function method_orderpatch_file($path): Response
	{
		throw new MethodNotFoundException('method_orderpatch');

		return new Response();
	}

	public function method_orderpatch_folder($path): Response
	{
		throw new MethodNotFoundException('method_orderpatch');

		return new Response();
	}

	/*



		#[Route('{file}', requirements: ['file' => '.*'], methods: ['LOCK'])]
		public function copy(Request $request, $path): Response
		{
			echo $this->requestStack->getMainRequest()->getContent();


			$parser = xml_parser_create();
			xml_parse($parser, $this->requestStack->getMainRequest()->getContent(), true); // finalisation de l'analyse


			$domObj = new xmlToArrayParser($parser);

			$domArr = $domObj->array;


			if($domObj->parse_error) echo $domObj->get_xml_error();

			else print_r($domArr);


			xml_parser_free($parser);

			$check = $this->fileManager->check($path);
			if($check == null)
			{
				return new Response($path . ' not exists', 404);
			}
			elseif($check == FileManagerInterface::TYPE_FILE)
			{
				//$this->fileManager->copyFile($path, $this->requestStack->getMainRequest()->headers->get('destination'));
			}
			elseif($check == FileManagerInterface::TYPE_FOLDER)
			{
				//$this->fileManager->copyDirectory($path, $this->requestStack->getMainRequest()->headers->get('destination'));
			}
			else
			{
				return new Response('evenement non géré', 500);
			}

			$ret = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?>
	<d:prop xmlns:d="DAV:">
	  <d:lockdiscovery>
		<d:activelock>
		</d:activelock>
	  </d:lockdiscovery>
	</d:prop>');

			/*      <d:locktype>
				   <d:transaction><d:groupoperation/></d:transaction>
				</d:locktype>
				<d:lockscope><d:local/></d:lockscope>
				<d:depth>0</d:depth>
				<d:owner>
				  <d:href>https://www.contoso.com/public/contact.html</d:href>
				</d:owner>
				<d:timeout>Second-604800</d:timeout>
				<d:locktoken>
				  <d:href>opaquelocktoken:e71df4fae-5dec-22d6-fea5-00a0c91e6be4</d:href>
				</d:locktoken>
		  */
	/* return new Response('', 200);
	}

	/*

	BCOPY Method

BDELETE Method

BMOVE Method

BPROPFIND Method

BPROPPATCH Method

COPY Method

DELETE Method

//LOCK Method

//MKCOL Method

//MOVE Method

NOTIFY Method

POLL Method

//PROPFIND Method

PROPPATCH Method

SEARCH Method

SUBSCRIBE Method

UNLOCK Method

UNSUBSCRIBE Method

X-MS-ENUMATTS Method

	 */
}
