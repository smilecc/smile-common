<?php


namespace Smile\Common\Support\Exception;


use Hyperf\Server\Exception\ServerException;
use Throwable;

class UnauthorizedException extends ServerException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
