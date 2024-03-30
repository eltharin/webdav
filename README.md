Symfony WebDav Bundle
==========================

[![Latest Stable Version](http://poser.pugx.org/eltharin/webdav/v)](https://packagist.org/packages/eltharin/webdav) 
[![Total Downloads](http://poser.pugx.org/eltharin/webdav/downloads)](https://packagist.org/packages/eltharin/webdav) 
[![Latest Unstable Version](http://poser.pugx.org/eltharin/webdav/v/unstable)](https://packagist.org/packages/eltharin/webdav) 
[![License](http://poser.pugx.org/eltharin/webdav/license)](https://packagist.org/packages/eltharin/webdav)

What is WebDav Bundle?
---------------------------
This bundle allow to create a webdav server running in your symfony application, you can use same credentials and have the same ACL logic.


Installation
------------

* Require the bundle with composer:

``` bash
composer require eltharin/webdav
```

and then add a file for add the routes loader : 

``` yaml
eltharin_webdav:
    resource: Eltharin\WebdavBundle\Routing\Loader
    type: service
```

this file can be found in /vendor/eltharin/webdav/config/routes and put in /config/routes

If you want enable Basic Authenticator for at less one of your configurations, you have to add a firewall in your security.yaml : 

```yaml
webdav:
    request_matcher: Eltharin\WebdavBundle\Security\WebdavRequestMatcher
    custom_authenticator: Eltharin\WebdavBundle\Security\WebdavAuthenticator
    entry_point: Eltharin\WebdavBundle\Security\WebdavAuthenticationEntryPoint
```

Configuration
-------------

* Create a configuration, for one webdav space, you can have as configurations you want, each have to start by only one start :

Start creating PHP Classe whitch extends Eltharin\WebdavBundle\Interface\AbstractWebDavConfiguration you have two stubs to implements, getRouteData and getFileManager : 

``` php
class WebDavConfiguration extends AbstractWebDavConfiguration
{
    public function getRouteData(): RouteData
    {
        // TODO: Implement getRouteData() method.
    }

    public function getFileManager(): FileManagerInterface
    {
        // TODO: Implement getFileManager() method.
    }
}
```

RouteData is an object represents the route used to obtain this webdav endpoint. It seems the basic symfony route configuration, you must set the path to access this endpoint, and you can set requirement too.

The webdav file path will be set a the end of this string, don't call your variables path.


``` php
    public function getRouteData(): RouteData
    {
        return new RouteData('/webdav');
    }
```

or

``` php
    public function getRouteData(): RouteData
    {
        return new RouteData('/webdav/{uuid}', ['uuid' => '\d+']);
    }
```

the generated routes will be /webdav/{path} and /webdav/{uuid}/path where path will be .*, path can contain / caracter for subfolders.


There is one fileManager for storing files on disk, root folder is configurable for this example %root%/var/data/ create this directory and add fileManager configuration : 

``` php
    public function getFileManager(): FileManagerInterface
    {
        $fileManager = new WebDavFileManager();
        $fileManager->setConfig($this->appKernel->getProjectDir() . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR,
            $this->requestStack->getMainRequest()?->getSchemeAndHttpHost() ?? '' ,
            '/' . $this->getRouteData()->getPath());
        return $fileManager;
    }
```

and we have our configuration : 

``` php
use Eltharin\WebdavBundle\FileManager\WebDavFileManager;
use Eltharin\WebdavBundle\Interface\AbstractWebDavConfiguration;
use Eltharin\WebdavBundle\Interface\FileManagerInterface;
use Eltharin\WebdavBundle\Routing\RouteData;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\KernelInterface;

class WebDavConfiguration extends AbstractWebDavConfiguration
{
    public function __construct(private KernelInterface $appKernel, private RequestStack $requestStack)
    {
    }
    
    public function getRouteData(): RouteData
    {
        return new RouteData('/webdav');
    }

    public function getFileManager(): FileManagerInterface
    {
        $fileManager = new WebDavFileManager();
        $fileManager->setConfig($this->appKernel->getProjectDir() . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR,
            $this->requestStack->getMainRequest()?->getSchemeAndHttpHost() ?? '' ,
            $this->getRouteData()->getPath()
        );
        return $fileManager;
    }
}
```



Add more security
----------------
for add security, WebDAv bundle include Authenticator and EntryPoint for Basic Authentication, verify you have add the firewall as say in installation part.

in your configuration where you want add security, you must add the security manager in the construct function for fill $securityManager variable: 

```php
public function __construct(private KernelInterface $appKernel, private RequestStack $requestStack, BasicAuthManager $authManager)
{
    $this->securityManager = $authManager;
}
```

and add an access_control item in your security.yaml file : 

```yaml
access_control:
    - { path: ^/webdav, roles: ROLE_USER }
```

Now you must be connected with ROLE_USER to access your files.

If you want more security levels, you can extends WebDavFileManager Class and implements the checkAutorization function or write your own class. 

TODO:
-------
- DB File Manager
- CalDav CardDav
