<?php

namespace app\core;

abstract class Controller
{
    protected $middlewares = [];
    public $action;
    protected $model = null;

    public function __construct(Model $model = null)
    {
        $this->model = $model;
    }

    public function validate(Request $request): bool
    {
        $this->model->load($request->getBody());
        return $this->model->validate();
    }

    public function save(): bool
    {
        return $this->model->save();
    }

    /**
     * @param string $view
     * @param array $params
     * @return array|false|string|string[]
     */
    public function render(string $view, array $params = [])
    {
        return Application::$app->view->renderView($view, $params);
    }

    /**
     * @param string $layout
     * @return void
     */
    public function setLayout(string $layout)
    {
        Application::$app->view->setLayout($layout);
    }

    public function registerMiddleware(Middleware $middleware)
    {
        $this->middlewares[] = $middleware;
    }

    /**
     * @return array
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }
}