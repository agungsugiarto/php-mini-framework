<?php

namespace Mini\Framework\Routing;

use Illuminate\View\View;
use Laminas\Diactoros\Response\HtmlResponse;
use League\Route\Route;
use League\Route\Strategy\ApplicationStrategy;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class Strategy extends ApplicationStrategy
{
    public function invokeRouteCallable(Route $route, ServerRequestInterface $request): ResponseInterface
    {
        $response = $route->getCallable($this->getContainer())($request, $route->getVars());

        if ($response instanceof ResponseInterface) {
            return $this->decorateResponse($response);
        }

        if ($response instanceof View) {
            return $this->decorateResponse(new HtmlResponse($response->toHtml()));
        }

        return $this->decorateResponse($response);
    }
}