<?php

namespace app\core\exceptions;

class ForbiddenMethodCalled extends \Exception
{
    protected $code = 403;
    protected $message = 'Forbidden method called!';
}