<?php

namespace hussainalihussain\phpmvclaravelclonecore\exceptions;

class ForbiddenMethodCalled extends \Exception
{
    protected $code = 403;
    protected $message = 'Forbidden method called!';
}