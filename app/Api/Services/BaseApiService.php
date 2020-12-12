<?php

namespace Api\Services;

use Api\Filters\BaseApiFilter;
use Api\Repositories\BaseApiRepository;
use Api\Repositories\Criterias\Core\DataCriteria;
use Api\Transformers\BaseApiTransformer;
use Api\Validators\BaseApiValidator;
use Illuminate\Support\Arr;

/**
 * Class BaseService
 * @package Api\Services
 */
abstract class BaseApiService
{
    /**
     * @var BaseApiFilter
     */
    protected $filter;

    /**
     * @var BaseApiValidator
     */
    protected $validator;

    /**
     * @var BaseApiRepository
     */
    protected $repository;

    /**
     * @var BaseApiTransformer
     */
    protected $transformer;

    /**
     * @var array
     */
    protected $listable = [
        'value' => 'id',
        'text' => 'name',
    ];

    /**
     * BaseApiService constructor.
     * @param BaseApiFilter $filter
     * @param BaseApiValidator $validator
     * @param BaseApiRepository $repository
     * @param BaseApiTransformer $transformer
     */
    public function __construct(
        BaseApiFilter $filter,
        BaseApiValidator $validator,
        BaseApiRepository $repository,
        BaseApiTransformer $transformer
    )
    {
        $this->filter = $filter;
        $this->validator = $validator;
        $this->repository = $repository;
        $this->transformer = $transformer;
    }

    /**
     * @param $abstract
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function setFilter($abstract)
    {
        $this->filter = app()->make($abstract);
    }

    /**
     * @param $abstract
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function setValidator($abstract)
    {
        $this->validator = app()->make($abstract);
    }

    /**
     * @param $abstract
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function setRepository($abstract)
    {
        $this->repository = app()->make($abstract);
    }

    /**
     * @param $abstract
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function setTransformer($abstract)
    {
        $this->transformer = app()->make($abstract);
    }

    /**
     * @param $id
     * @param array $data
     * @return array|mixed
     * @throws \Exception
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function single($id, $data = [])
    {
        $this->validator
            ->setData($data)
            ->addParam('id', $id)
            ->validate('single');

        $this->applyFixes($data);

        return $this->transformer->transform($this->repository->find($id));
    }

    /**
     * @param array $data
     * @return array
     * @throws \Exception
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function collection($data = [])
    {
        $this->validator
            ->setData($data)
            ->validate('collection');

        $this->applyFixes($data);

        return $this->transformer->transformCollection($this->repository->get());
    }

    /**
     * @param array $data
     * @return array
     * @throws \Exception
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function pagination($data = [])
    {
        $this->validator
            ->setData($data)
            ->validate('pagination');

        $this->applyFixes($data);

        $limit = $data['limit'] ?? config('api.pagination.limit');

        return $this->transformer->transformPagination($this->repository->paginate($limit));
    }

    /**
     * @param array $data
     * @return array
     * @throws \Exception
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function listable($data = [])
    {
        $selects = [
            $this->listable['value'],
            $this->listable['text'],
        ];

        $this->fixData([
            '_data' => [
                'selects' => $selects,
            ]
        ]);
        $this->fixFilters($data);

        return $this->transformer->transformList(
            $this->repository->get(),
            $this->listable['value'],
            $this->listable['text']
        );
    }

    /**
     * @param array $data
     * @return mixed
     * @throws \Exception
     */
    public function create(array $data)
    {
        $this->validator
            ->setData($data)
            ->validate('create');

        $this->repository->startTransaction();

        try {
            $result = $this->baseCreate($data);

            $this->repository->commitTransaction();

            return $result;
        } catch (\Exception $exception) {
            $this->repository->rollBackTransaction();
            throw $exception;
        }
    }

    /**
     * @param $id
     * @param array $data
     * @return mixed
     * @throws \Exception
     */
    public function update($id, array $data)
    {
        $this->validator
            ->setData($data)
            ->addParam('id', $id)
            ->validate('update');

        $this->repository->startTransaction();

        try {
            $result = $this->baseUpdate($id, $data);

            $this->repository->commitTransaction();

            return $result;
        } catch (\Exception $exception) {
            $this->repository->rollBackTransaction();
            throw $exception;
        }
    }

    /**
     * @param $id
     * @param array $data
     * @return mixed
     * @throws \Exception
     */
    public function replace($id, array $data)
    {
        $this->validator
            ->setData($data)
            ->addParam('id', $id)
            ->validate('replace');

        $this->repository->startTransaction();

        try {
            $result = $this->baseReplace($id, $data);

            $this->repository->commitTransaction();

            return $result;
        } catch (\Exception $exception) {
            $this->repository->rollBackTransaction();
            throw $exception;
        }
    }

    /**
     * @param $id
     * @return int
     * @throws \Exception
     */
    public function delete($id)
    {
        $this->validator
            ->addParam('id', $id)
            ->validate('delete');

        $this->repository->startTransaction();

        try {
            $result = $this->baseDelete($id);

            $this->repository->commitTransaction();

            return $result;
        } catch (\Exception $exception) {
            $this->repository->rollBackTransaction();
            throw $exception;
        }
    }

    /**
     * @param array $data
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    protected function baseCreate(array $data)
    {
        return $this->repository->create($data);
    }

    /**
     * @param $id
     * @param array $data
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    protected function baseUpdate($id, array $data)
    {
        return $this->repository->update($data, $id);
    }

    /**
     * @param $id
     * @param array $data
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    protected function baseReplace($id, array $data)
    {
        return $this->repository->update($data, $id);
    }

    /**
     * @param $id
     * @return int
     */
    protected function baseDelete($id)
    {
        return $this->repository->delete($id);
    }

    /**
     * @param array $data
     * @return $this
     * @throws \Exception
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    protected function applyFixes(array $data)
    {
        $this->fixData($data);
        $this->fixFilters($data);

        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    protected function fixFilters(array $data)
    {
        $this->repository = $this->filter
            ->setData($data)
            ->setRepository($this->repository)
            ->filter();

        return $this;
    }

    /**
     * @param array $data
     * @return $this
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    protected function fixData(array $data)
    {
        if (!Arr::get($data, '_data')) {
            return $this;
        }

        $this->repository->pushCriteria(new DataCriteria($data['_data']));

        return $this;
    }
}
