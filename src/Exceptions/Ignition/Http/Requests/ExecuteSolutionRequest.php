<?php

namespace Mini\Framework\Exceptions\Ignition\Http\Requests;

use Mini\Framework\Http\FormRequest;
use Spatie\Ignition\Contracts\RunnableSolution;
use Spatie\Ignition\Contracts\Solution;
use Spatie\Ignition\Contracts\SolutionProviderRepository;

class ExecuteSolutionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'solution' => 'required',
            'parameters' => 'array',
        ];
    }

    public function getSolution(): Solution
    {
        $solution = app(SolutionProviderRepository::class)
            ->getSolutionForClass($this->request->getQueryParams()['solution']);

        abort_if(is_null($solution), 404, 'Solution could not be found');

        return $solution;
    }

    public function getRunnableSolution(): RunnableSolution
    {
        $solution = $this->getSolution();

        if (! $solution instanceof RunnableSolution) {
            abort(404, 'Runnable solution could not be found');
        }

        return $solution;
    }
}
