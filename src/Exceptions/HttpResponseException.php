<?php

namespace Mini\Framework\Exceptions;

use Laminas\Diactoros\Response;
use RuntimeException;

class HttpResponseException extends RuntimeException
{
    /**
     * The underlying response instance.
     *
     * @var \Laminas\Diactoros\Response
     */
    protected $response;

    /**
     * Create a new HTTP response exception instance.
     *
     * @return void
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * Get the underlying response instance.
     *
     * @return \Laminas\Diactoros\Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}
