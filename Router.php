<?php

namespace hussainalihussain\phpmvclaravelclonecore;

use hussainalihussain\phpmvclaravelclonecore\exceptions\ForbiddenMethodCalled;
use hussainalihussain\phpmvclaravelclonecore\exceptions\NotFoundException;

class Router
{
    const METHOD_GET  = 'get';
    const METHOD_POST = 'post';
    public $routes    = [];
    /**
     * @var Request
     */
    public $request;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param string $url
     * @param $callback
     * @return void
     */
    public function get(string $url, $callback)
    {
        $this->routes[self::METHOD_GET][$url] = $callback;
    }

    /**
     * @param string $url
     * @param $callback
     * @return void
     */
    public function post(string $url, $callback)
    {
        $this->routes[self::METHOD_POST][$url] = $callback;
    }

    /**
     * @return array|false|mixed|string|string[]
     * @throws NotFoundException
     * @throws ForbiddenMethodCalled
     */
    public function resolve()
    {
        $method   = $this->request->method();
        $path     = $this->request->path();
        $callback = $this->routes[$method][$path] ?? null;

        if(!$callback)
        {
            $anotherMethod   = $method == 'get' ? 'post' : 'get';
            $anotherCallback = $this->routes[$anotherMethod][$path] ?? null;

            if($anotherCallback)
            {
                throw new ForbiddenMethodCalled();
            }

            throw new NotFoundException();
        }

        if(is_string($callback))
        {
            return Application::$app->view->renderView($callback);
        }

        if(is_array($callback))
        {
            $callback[0]                          = new $callback[0]();
            Application::$app->controller         = $callback[0];
            Application::$app->controller->action = $callback[1];
            $this->runMiddlewares();
        }

        return call_user_func($callback, Application::$app->request, Application::$app->response);
    }

    public function runMiddlewares()
    {
        foreach (Application::$app->controller->getMiddlewares() as $middleware)
        {
            $middleware->execute();
        }
    }

}