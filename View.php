<?php

namespace app\core;

class View
{
    public $layout;
    public $title = 'Title goes here';

    /**
     * @param string $layout
     */
    public function __construct(string $layout = 'main')
    {
        $this->setLayout($layout);
    }

    /**
     * @param string $layout
     * @return void
     */
    public function setLayout(string $layout)
    {
        $this->layout = $layout;
    }

    /**
     * @param string $view
     * @param array $params
     * @return array|false|string|string[]
     */
    public function renderView(string $view, array $params = [])
    {
        $layout      = $this->renderLayout();
        $view        = str_replace('.', '/', $view);
        $viewContent = $this->getViewContent($view, $params);

        return str_replace('{{content}}', $viewContent, $layout);
    }

    /**
     * @return false|string
     */
    public function renderLayout()
    {
        ob_start();
        require Application::$ROOT_PATH . "/views/layouts/{$this->layout}.php";
        return ob_get_clean();
    }

    /**
     * @param $view
     * @param $params
     * @return false|string
     */
    public function getViewContent($view, $params = [])
    {
        foreach ($params as $variable=> $value)
        {
            ${$variable} = $value;
        }

        ob_start();

        require Application::$ROOT_PATH . "/views/{$view}.php";

        return ob_get_clean();
    }
}