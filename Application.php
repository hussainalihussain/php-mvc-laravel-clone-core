<?php

namespace hussainalihussain\phpmvclaravelclonecore;

use hussainalihussain\phpmvclaravelclonecore\database\Database;
use app\models\User;

class Application
{
    /**
     * @var Router
     */
    public $router;
    /**
     * @var Request
     */
    public $request;
    /**
     * @var Response
     */
    public $response;
    /**
     * @var View
     */
    public $view;
    /**
     * @var Application
     */
    public static $app;
    /**
     * @var string
     */
    public static $ROOT_PATH;

    /**
     * @var Database
     */
    public $db;

    /**
     * @var Session
     */
    public $session;

    /**
     * @var Controller
     */
    public $controller;

    public $userClassName;

    /**
     * @param string $root_path
     * @param array $config
     */
    public function __construct(string $root_path, array $config = [])
    {
        self::$ROOT_PATH     = $root_path;
        $this->request       = new Request();
        $this->router        = new Router($this->request);
        $this->response      = new Response();
        $this->view          = new View($config['layout'] ?? '');
        self::$app           = $this;
        $this->db            = new Database($config['db'] ?? []);
        $this->session       = new Session();
        $this->userClassName = $config['userClassName'] ?? '';
    }

    /**
     * @return void
     */
    public function run()
    {
        try
        {
            echo $this->router->resolve();
        }
        catch (\Exception $e)
        {
            $this->response->setCode($e->getCode());
            echo $this->view->renderView('_error', [
                'exception'=> $e
            ]);
        }
    }

    public function login(int $id): bool
    {
        $this->session->set('user', $id);
        return true;
    }

    public static function isGuest(): bool
    {
        return !Application::$app->isLoggedIn();
    }

    public static function user()
    {
        if(($id = Application::$app->isLoggedIn()) === false)
        {
            return false;
        }

        return Application::$app->userClassName::find([
            Application::$app->userClassName::primaryKey()=> $id
        ]);
    }

    public function logout(): bool
    {
        return $this->session->remove('user');
    }

    public function isLoggedIn()
    {
        return $this->session->get('user') ?? false;
    }
}