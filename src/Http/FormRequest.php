<?php

namespace Mini\Framework\Http;

use Mini\Framework\Routing\ProvidesConvenienceMethods;
use Psr\Http\Message\ServerRequestInterface;

abstract class FormRequest
{
    use ProvidesConvenienceMethods;

    protected $validate;

    public function __construct(ServerRequestInterface $request)
    {
        $this->validate = $this->validate($request, $this->rules());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    abstract public function rules();

    /**
     * Get the validated data from the request.
     *
     * @param  string|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function validated($key = null, $default = null)
    {
        return data_get($this->validate, $key, $default);
    }
}