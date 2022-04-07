<?php

namespace app\core\form;

use app\core\Model;

class Form
{
    public const METHOD_GET  = 'get';
    public const METHOD_POST = 'post';

    public $action;
    public $method;
    public $model;

    public function __construct(string $action, string $method, Model $model)
    {
        $this->action = $action;
        $this->method = $method;
        $this->model  = $model;
    }

    public static function begin(string $action, string $method, Model $model)
    {
        return new Form($action, $method, $model);
    }

    public function __toString()
    {
        return sprintf('<form action="%s" method="%s">', $this->action, $this->method);
    }

    public static function end()
    {
        return '</form>';
    }

    public function field(string $name): InputField
    {
        return new InputField($name, $this->model);
    }

    public function textarea(string $name): TextareaField
    {
        return new TextareaField($name, $this->model);
    }
}