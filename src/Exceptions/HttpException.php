<?php

namespace Mini\Framework\Exceptions;

use Laminas\Diactoros\Exception\ExceptionInterface;

class HttpException extends \RuntimeException implements ExceptionInterface
{
    private $statusCode;

    private $headers;

    public function __construct(int $statusCode, ?string $message = '', \Throwable $previous = null, array $headers = [], ?int $code = 0)
    {
        $this->statusCode = $statusCode;
        $this->headers = $headers;

        parent::__construct($message, $code, $previous);
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Set response headers.
     *
     * @param array $headers Response headers
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }
}
