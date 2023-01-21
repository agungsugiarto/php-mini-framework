<?php

namespace Mini\Framework\Exceptions;

interface HttpExceptionInterface extends \Throwable
{
    /**
     * Returns the status code.
     *
     * @return int
     */
    public function getStatusCode();

    /**
     * Returns response headers.
     *
     * @return array
     */
    public function getHeaders();
}