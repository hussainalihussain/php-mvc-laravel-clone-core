<?php

namespace hussainalihussain\phpmvclaravelclonecore;

class Response
{
    /**
     * @param $code
     * @return void
     */
    public function setCode($code)
    {
        http_response_code($code);
    }

    public function redirect($url)
    {
        header("Location: {$url}");
        exit;
    }
}