<?php

namespace Mini\Framework\Routing;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;
use Illuminate\Contracts\Bus\Dispatcher;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use Illuminate\Validation\ValidationException;

trait ProvidesConvenienceMethods
{
    /**
     * The response builder callback.
     *
     * @var \Closure
     */
    protected static $responseBuilder;

    /**
     * The error formatter callback.
     *
     * @var \Closure
     */
    protected static $errorFormatter;

    /**
     * Set the response builder callback.
     *
     * @param  \Closure  $callback
     * @return void
     */
    public static function buildResponseUsing(Closure $callback)
    {
        static::$responseBuilder = $callback;
    }

    /**
     * Set the error formatter callback.
     *
     * @param  \Closure  $callback
     * @return void
     */
    public static function formatErrorsUsing(Closure $callback)
    {
        static::$errorFormatter = $callback;
    }

    /**
     * Validate the given request with the given rules.
     *
     * @param  \Psr\Http\Message\ServerRequestInterface  $request
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $customAttributes
     * @return array
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validate(ServerRequestInterface $request, array $rules, array $messages = [], array $customAttributes = [])
    {
        $validator = $this->getValidationFactory()->make(
            array_unique(array_merge(
                $request->getQueryParams(),
                $request->getParsedBody(),
                $request->getUploadedFiles()
            )),
            $rules,
            $messages,
            $customAttributes
        );

        if ($validator->fails()) {
            $this->throwValidationException($request, $validator);
        }

        return $this->extractInputFromRules($rules);
    }

    /**
     * Get the request input based on the given validation rules.
     *
     * @param  array  $rules
     * @return array
     */
    protected function extractInputFromRules(array $rules)
    {
        return collect($rules)->keys()->map(function ($rule) {
            return Str::contains($rule, '.') ? explode('.', $rule)[0] : $rule;
        })->unique()->toArray();
    }

    /**
     * Throw the failed validation exception.
     *
     * @param  \Psr\Http\Message\ServerRequestInterface  $request
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function throwValidationException(ServerRequestInterface $request, $validator)
    {
        throw new ValidationException($validator, $this->buildFailedValidationResponse(
            $request, $this->formatValidationErrors($validator)
        ));
    }

    /**
     * Build a response based on the given errors.
     *
     * @param  \Psr\Http\Message\ServerRequestInterface  $request
     * @param  array  $errors
     * @return \Laminas\Diactoros\Response\JsonResponse|mixed
     */
    protected function buildFailedValidationResponse(ServerRequestInterface $request, array $errors)
    {
        if (isset(static::$responseBuilder)) {
            return (static::$responseBuilder)($request, $errors);
        }

        return new JsonResponse($errors, 422);
    }

    /**
     * Format validation errors.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return array|mixed
     */
    protected function formatValidationErrors(Validator $validator)
    {
        if (isset(static::$errorFormatter)) {
            return (static::$errorFormatter)($validator);
        }

        return $validator->errors()->getMessages();
    }


    /**
     * Dispatch a job to its appropriate handler.
     *
     * @param  mixed  $job
     * @return mixed
     */
    public function dispatch($job)
    {
        return app(Dispatcher::class)->dispatch($job);
    }

    /**
     * Dispatch a command to its appropriate handler in the current process.
     *
     * @param  mixed  $job
     * @param  mixed  $handler
     * @return mixed
     */
    public function dispatchNow($job, $handler = null)
    {
        return app(Dispatcher::class)->dispatchNow($job, $handler);
    }

    /**
     * Get a validation factory instance.
     *
     * @return \Illuminate\Contracts\Validation\Factory
     */
    protected function getValidationFactory()
    {
        return app('validator');
    }
}
