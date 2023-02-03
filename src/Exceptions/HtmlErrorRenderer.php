<?php

namespace Mini\Framework\Exceptions;

use Symfony\Component\ErrorHandler\ErrorRenderer\ErrorRendererInterface;
use Symfony\Component\ErrorHandler\ErrorRenderer\HtmlErrorRenderer as BaseHtmlErrorRenderer;
use Throwable;
use Symfony\Component\ErrorHandler\Exception\FlattenException;

class HtmlErrorRenderer implements ErrorRendererInterface
{
    private bool|\Closure $debug;

    public function __construct(bool|callable $debug = false)
    {
        $this->debug = $debug;
    }

    /**
     * {@inheritdoc}
     */
    public function render(Throwable $exception): FlattenException
    {
        $statusCode = null;

        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
        }

        $renderer = new BaseHtmlErrorRenderer($this->debug);

        $headers = ['Content-Type' => 'text/html; charset='.invade($renderer)->charset];
        if (\is_bool($this->debug) ? $this->debug : ($this->debug)($exception)) {
            $headers['X-Debug-Exception'] = rawurlencode($exception->getMessage());
            $headers['X-Debug-Exception-File'] = rawurlencode($exception->getFile()).':'.$exception->getLine();
        }

        $exception = FlattenException::createFromThrowable($exception, $statusCode, $headers);

        return $exception->setAsString(invade($renderer)->renderException($exception));
    }
}
