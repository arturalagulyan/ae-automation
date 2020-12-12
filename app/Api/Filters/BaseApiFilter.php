<?php

namespace Api\Filters;

use Api\Repositories\BaseApiRepository;
use Api\Repositories\Criterias\Core\LimitCriteria;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Class BaseApiFilter
 * @package Api\Filters
 */
abstract class BaseApiFilter
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @var array
     */
    protected $selects;

    /**
     * @var BaseApiRepository
     */
    protected $repository;

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
     * @param BaseApiRepository $repository
     * @return $this
     */
    public function setRepository(BaseApiRepository $repository)
    {
        $this->repository = $repository;

        return $this;
    }

    /**
     * @return BaseApiRepository
     */
    public function filter()
    {
        if (method_exists($this, 'filterByDefault')) {
            $this->filterByDefault();
        }

        $this->applySorts();
        $this->applyFilters();

        return $this->repository;
    }

    /**
     * @return bool
     */
    protected function applyFilters()
    {
        if (empty($this->data)) {
            return false;
        }

        foreach ($this->data as $key => $value) {
            $method = 'filterBy' . ucfirst(Str::camel($key));

            if (!method_exists($this, $method)) {
                continue;
            }

            if (!empty($value)) {
                $this->{$method}($value);
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function applySorts()
    {
        $order = Arr::get($this->data, 'order');
        $sortBy = Arr::get($this->data, 'sortBy');

        if (!$order || !$sortBy) {
            return false;
        }

        $sortBy = is_array($sortBy) ? $sortBy : [$sortBy];

        foreach ($sortBy as $sort) {
            $method = 'sortBy' . ucfirst(Str::camel($sort));

            if (!method_exists($this, $method)) {
                continue;
            }

            $this->{$method}($order);
        }

        return true;
    }

    /**
     * @param $limit
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    protected function filterByLimit($limit)
    {
        $this->repository->pushCriteria(new LimitCriteria($limit));
    }
}
