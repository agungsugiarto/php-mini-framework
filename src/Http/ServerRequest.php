<?php

namespace Mini\Framework\Http;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\ServerBag;
use Symfony\Component\HttpFoundation\ParameterBag;
use Laminas\Diactoros\Exception\InvalidArgumentException;
use Laminas\Diactoros\ServerRequest as BaseServerRequest;
use Mini\Framework\Http\Concerns\InteractsWithContentTypes;
use Mini\Framework\Http\Concerns\InteractsWithFlashData;
use Mini\Framework\Http\Concerns\InteractsWithInput;

class ServerRequest extends BaseServerRequest
{
    use InteractsWithContentTypes;
    use InteractsWithFlashData;
    use InteractsWithInput;
    use Macroable;

    /**
     * Request body parameters ($_POST).
     *
     * @var InputBag
     */
    public $request;

    /**
     * Query string parameters ($_GET).
     *
     * @var InputBag
     */
    public $query;

    /**
     * Server and execution environment parameters ($_SERVER).
     *
     * @var ServerBag
     */
    public $server;

    /**
     * Cookies ($_COOKIE).
     *
     * @var InputBag
     */
    public $cookies;

    /**
     * The decoded JSON content for the request.
     *
     * @var ParameterBag|null
     */
    protected $json;

    /**
     * The route resolver callback.
     *
     * @var \Closure
     */
    protected $routeResolver;

    /**
     * @param array $serverParams Server parameters, typically from $_SERVER
     * @param array $uploadedFiles Upload file information, a tree of UploadedFiles
     * @param null|string|UriInterface $uri URI for the request, if any.
     * @param null|string $method HTTP method for the request, if any.
     * @param string|resource|StreamInterface $body Message body, if any.
     * @param array $headers Headers for the message, if any.
     * @param array $cookieParams Cookies for the message, if any.
     * @param array $queryParams Query params for the message, if any.
     * @param null|array|object $parsedBody The deserialized body parameters, if any.
     * @param string $protocol HTTP protocol version.
     * @throws InvalidArgumentException For any invalid value.
     */
    public function __construct(
        private array $serverParams = [],
        array $uploadedFiles = [],
        $uri = null,
        ?string $method = null,
        $body = 'php://input',
        array $headers = [],
        private array $cookieParams = [],
        private array $queryParams = [],
        private $parsedBody = null,
        string $protocol = '1.1'
    ) {
        parent::__construct(
            $serverParams,
            $uploadedFiles,
            $uri,
            $method,
            $body,
            $headers,
            $cookieParams,
            $queryParams,
            $parsedBody,
            $protocol
        );

        $this->request = new InputBag($this->getParsedBody());
        $this->query = new InputBag($this->getQueryParams());
        $this->server = new ServerBag($this->getServerParams());
        $this->cookies = new InputBag($this->getCookieParams());
    }

    /**
     * Gets a "parameter" value from any bag.
     *
     * This method is mainly useful for libraries that want to provide some flexibility. If you don't need the
     * flexibility in controllers, it is better to explicitly get request parameters from the appropriate
     * public property instead (attributes, query, request).
     *
     * Order of precedence: PATH (routing placeholders or custom attributes), GET, POST
     *
     * @param mixed $default The default value if the parameter key does not exist
     *
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        if ($result = $this->getAttribute($key)) {
            return $result;
        }

        if ($this->query->has($key)) {
            return $this->query->all()[$key];
        }

        if ($this->request->has($key)) {
            return $this->request->all()[$key];
        }

        return $default;
    }

    /**
     * Retrieve an input item from the request.
     *
     * @param  string|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function input($key = null, $default = null)
    {
        return data_get(
            $this->getInputSource()->all() + $this->query->all(), $key, $default
        );
    }

    /**
     * Get the JSON payload for the request.
     *
     * @param  string|null  $key
     * @param  mixed  $default
     * @return \Symfony\Component\HttpFoundation\ParameterBag|mixed
     */
    public function json($key = null, $default = null)
    {
        if (! isset($this->json)) {
            $this->json = new ParameterBag((array) json_decode($this->getBody()->getContents(), true));
        }

        if (is_null($key)) {
            return $this->json;
        }

        return data_get($this->json->all(), $key, $default);
    }

    /**
     * Get the input source for the request.
     *
     * @return \Symfony\Component\HttpFoundation\ParameterBag
     */
    protected function getInputSource()
    {
        if ($this->isJson()) {
            return $this->json();
        }

        return in_array($this->getMethod(), ['GET', 'HEAD']) ? $this->query : $this->request;
    }

    /**
     * Determine if the route name matches a given pattern.
     *
     * @param mixed $patterns
     *
     * @return bool
     */
    public function routeIs(...$patterns)
    {
        if (! Arr::exists($route = $this->route()[1], 'as')) {
            return false;
        }

        foreach ($patterns as $pattern) {
            if (Str::is($pattern, $route['as'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the route handling the request.
     *
     * @param string|null $param
     * @param mixed       $default
     *
     * @return array|string
     */
    public function route($param = null, $default = null)
    {
        $route = ($this->getRouteResolver())();

        if (is_null($route) || is_null($param)) {
            return $route;
        }

        return Arr::get($route[2], $param, $default);
    }

    /**
     * Get the route resolver callback.
     *
     * @return \Closure
     */
    public function getRouteResolver()
    {
        return $this->routeResolver ?: function () {
            //
        };
    }

    /**
     * Set the route resolver callback.
     *
     * @return $this
     */
    public function setRouteResolver(Closure $callback)
    {
        $this->routeResolver = $callback;

        return $this;
    }
}
