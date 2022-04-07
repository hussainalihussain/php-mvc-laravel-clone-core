<?php

namespace app\core;

/**
 * @property mixed|void $email
 * @property mixed|void $password
 */
class Request
{
    /**
     * @return string
     */
    public function method(): string
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function __get($name)
    {
        $body = $this->getBody();

        if(array_key_exists($name, $body))
        {
            return $body[$name];
        }
    }

    /**
     * @return bool
     */
    public function isPost(): bool
    {
        return $this->method() === 'post';
    }

    /**
     * @return bool
     */
    public function isGet(): bool
    {
        return $this->method() === 'get';
    }

    /**
     * @return false|mixed|string
     */
    public function path()
    {
        $path = $_SERVER['REQUEST_URI'];

        if(false === ($position = strpos($path, '?')))
        {
            return $path;
        }

        return substr($path, 0, $position);
    }

    /**
     * @return array
     */
    public function getBody(): array
    {
        return $_REQUEST;
    }
}