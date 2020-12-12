<?php

namespace Api\Validators;

use Api\Validators\Traits\Authorize;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * Class BaseApiValidator
 * @package Api\Validators
 */
abstract class BaseApiValidator
{
    use Authorize;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var array
     */
    protected $rules = [];

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @var array
     */
    protected $messages = [];

    /**
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        return Arr::get($this->data, $key);
    }

    /**
     * @param $param
     * @return mixed
     */
    public function param($param)
    {
        return Arr::get($this->params, $param);
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param array $params
     * @return $this
     */
    public function setParams(array $params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * @param string $param
     * @param $value
     * @return BaseApiValidator
     */
    public function addParam($param, $value)
    {
        $this->params[$param] = $value;

        return $this;
    }

    /**
     * @param string $field
     * @param $rule
     * @param array $arguments
     * @return BaseApiValidator
     */
    public function addRule($field, $rule, $arguments = [])
    {
        $this->rules[$field][] = $this->formatRule($rule, $arguments);

        return $this;
    }

    /**
     * @param string $field
     * @param string $rule
     * @return BaseApiValidator
     */
    public function removeRule($field, $rule)
    {
        foreach ($this->rules[$field] as $index => $ruleOptions) {
            if ($this->deFormatRule($ruleOptions) === $rule) {
                unset($this->rules[$field][$index]);
                $this->rules[$field] = array_values($this->rules[$field]);
                break;
            }
        }

        return $this;
    }

    /**
     * @param $rule
     * @param array $arguments
     * @return $this
     */
    public function appendRule($rule, $arguments = [])
    {
        foreach ($this->rules as $field => $rules) {
            $this->addRule($field, $rule, $arguments);
        }

        return $this;
    }

    /**
     * @param string $field
     * @param string $rule
     * @param string $message
     * @return BaseApiValidator
     */
    public function addMessage($field, $rule, $message)
    {
        $this->messages["$field.$rule"] = $message;

        return $this;
    }

    /**
     * @param $method
     * @return bool
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validate($method)
    {
        $action = ucfirst(Str::camel($method));

        $this->callValidate($action);
        $this->callAuthorize($action);

        return true;
    }

    /**
     * @param $action
     * @return bool
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function callValidate($action)
    {
        $validation = 'validate' . $action;

        if (!method_exists($this, $validation)) {
            return false;
        }

        $this->{$validation}();

        Validator::make(
            $this->data,
            $this->rules,
            $this->messages
        )->validate();

        return true;
    }

    /**
     * @param $rule
     * @param array $arguments
     * @return string
     */
    protected function formatRule($rule, $arguments = [])
    {
        if (empty($arguments)) {
            return $rule;
        }
        $arguments = implode(',', $arguments);

        return sprintf('%s:%s', $rule, $arguments);
    }

    /**
     * @param string $rule
     * @return string
     */
    protected function deFormatRule($rule)
    {
        return explode(':', $rule)[0];
    }
}
