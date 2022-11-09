<?php

namespace Oooiik\LaravelQueryFilter\Services;

use Illuminate\Database\Eloquent\Builder;
use Oooiik\LaravelQueryFilter\Filters\QueryFilter;

class FilterService
{
    protected $client;

    protected $baseBuilder;

    protected $params = [];

    /**
     * @param QueryFilter|string $client
     * @param Builder $builder
     */
    public function __construct($client, Builder $builder)
    {
        if (is_string($client)) {
            $client = new $client();
        }
        $this->client = $client;
        $this->baseBuilder = $builder;
        $this->getClient()->builder = clone $this->baseBuilder;
    }

    protected function getBaseBuilder()
    {
        return $this->baseBuilder;
    }

    protected function getClient()
    {
        return $this->client;
    }

    protected function getClientBuilder()
    {
        return $this->getClient()->builder;
    }

    protected function getClientMethods()
    {
        return get_class_methods($this->getClient());
    }

    protected function getClientDefault(string $key = null)
    {
        if ($key === null) {
            return empty($this->getClient()->default) ? [] : $this->getClient()->default;
        } else {
            return array_key_exists($key, $this->getClientDefault()) ? $this->getClientDefault()[$key] : null;
        }
    }

    protected function getClientFallback(string $key = null)
    {
        if ($key === null) {
            return empty($this->getClient()->fallback) ? [] : $this->getClient()->fallback;
        } else {
            return array_key_exists($key, $this->getClientFallback()) ? $this->getClientFallback()[$key] : null;
        }
    }

    public function query()
    {
        return $this->getClientBuilder();
    }

    protected function setParams($params)
    {
        $this->params = array_merge($this->getClientDefault(), $params);
    }

    protected function getParam(string $key = null)
    {
        if ($key === null) {
            return $this->params;
        } else {
            return array_key_exists($key, $this->params) ? $this->params[$key] : null;
        }
    }

    protected function callMethod($method)
    {
        if (!in_array($method, $this->getClientMethods())) {
            throw new \BadMethodCallException("This method is not found in fallback!", 500);
        }
        call_user_func([$this->getClient(), $method], $this->getParam($method), $this->getParam());
    }

    protected function filterApply($method)
    {
        $paramsKeys = array_keys($this->getParam());
        $fallbackKeys = array_keys($this->getClientFallback());

        if (in_array($method, $paramsKeys)) {
            $this->callMethod($method);
        } elseif (in_array($method, $fallbackKeys)) {
            $this->callMethod($this->getClientFallback($method));
        }
    }

    public function apply(array $params = [])
    {
        $this->setParams($params);

        foreach ($this->getClientMethods() as $method) {
            $this->filterApply($method);
        }

        return $this;
    }

    public function resetApply()
    {
        $this->getClient()->builder = clone $this->getBaseBuilder();
        return $this;
    }
}