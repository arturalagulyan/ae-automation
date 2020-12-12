<?php

namespace Api\Repositories;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class BaseApiRepository
 * @package Api\Repositories
 */
abstract class BaseApiRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $changes = [];

    /**
     * @return array
     */
    public function fillables()
    {
        return $this->model->getFillable();
    }

    /**
     * @return array
     */
    public function relations()
    {
        return $this->model->getRelations();
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->model->getKey();
    }

    /**
     * @return string
     */
    public function getKeyName()
    {
        return $this->model->getKeyName();
    }

    /**
     * @param array $attributes
     * @param array $relations
     * @return mixed
     * @throws \Exception
     */
    public function createWith(array $attributes, $relations = [])
    {
        $this->startTransaction();

        try {
            $item = $this->create($attributes);
            $this->createRelations($item, $relations);

            $this->commitTransaction();

            return $item;
        } catch (\Exception $exception) {
            $this->rollBackTransaction();

            throw $exception;
        }
    }

    /**
     * @param $item
     * @param array $relations
     */
    protected function createRelations($item, array $relations)
    {
        foreach ($relations as $relation) {
            $query = $item->{$relation['name']}();

            if ($query instanceof HasOne || $query instanceof MorphOne) {
                if (isset($relation['attributes']) && is_array($relation['attributes'])) {
                    $relative = $query->updateOrCreate($relation['attributes'], $relation['data']);
                } else {
                    $relative = $query->create($relation['data']);
                }

                if (!empty($relation['relations']) && !empty($relative)) {
                    $this->createRelations($relative, $relation['relations']);
                }

                continue;
            }
            if ($query instanceof BelongsTo) {
                if (isset($relation['attributes']) && is_array($relation['attributes'])) {
                    $relative = $query->updateOrCreate($relation['attributes'], $relation['data']);
                } else {
                    $relative = $query->create($relation['data']);
                }

                $query->associate($relative);

                if (!empty($relation['relations']) && !empty($relative)) {
                    $this->createRelations($relative, $relation['relations']);
                }

                continue;
            }
            if ($query instanceof HasMany || $query instanceof MorphMany) {
                $relatives = $query->createMany($relation['data']);

                foreach ($relatives as $relative) {
                    if (!empty($relation['relations'])) {
                        $this->createRelations($relative, $relation['relations']);
                    }
                }

                continue;
            }
            if ($query instanceof BelongsToMany) {
                $query->attach($relation['data']);
            }
        }
    }

    /**
     * @param array $attributes
     * @param $id
     * @param array $relations
     * @return mixed
     * @throws \Exception
     */
    public function updateWith(array $attributes, $id, $relations = [])
    {
        $this->startTransaction();

        try {
            $item = $this->update($attributes, $id);
            $this->updateRelations($item, $relations);

            $this->commitTransaction();

            return $item;
        } catch (\Exception $exception) {
            $this->rollBackTransaction();

            throw $exception;
        }
    }

    /**
     * @TODO optimize SQL queries and sometimes use 'insert'
     *
     * @param $item
     * @param array $relations
     */
    protected function updateRelations($item, array $relations)
    {
        foreach ($relations as $relation) {
            $query = $item->{$relation['name']}();

            $isCleaned = false;

            if ($query instanceof HasOne || $query instanceof MorphOne) {
                if (isset($relation['clean']) && $relation['clean']) {
                    $query->delete();
                    $isCleaned = true;
                }
                if (isset($relation['data']) && $relation['data']) {
                    if ($isCleaned) {
                        $relative = $query->create($relation['data']);
                    } else {
                        $relative = $item->{$relation['name']};

                        if (empty($relative)) {
                            $relative = $query->create($relation['data']);
                        } else {
                            $relative->update($relation['data']);
                        }
                    }
                    if (!empty($relation['relations']) && !empty($relative)) {
                        $this->updateRelations($relative, $relation['relations']);
                    }
                }
                continue;
            }
            if ($query instanceof HasMany || $query instanceof MorphMany) {
                if (isset($relation['clean']) && $relation['clean']) {
                    $query->delete();
                    $isCleaned = true;
                }
                if (isset($relation['data']) && $relation['data']) {
                    if ($isCleaned) {
                        $query->createMany($relation['data']);
                    } else {
                        $relatives = $item->{$relation['name']};

                        if (!$relatives->count()) {
                            $query->createMany($relation['data']);
                        } else {
                            foreach ($relatives as $relative) {
                                foreach ($relation['data'] as $datum) {
                                    if (
                                        !isset($datum['id']) || empty($datum['id']) ||
                                        $datum['id'] !== $relative->id
                                    ) {
                                        continue;
                                    }

                                    $relative->update($datum);
                                }
                                if (!empty($relation['relations'])) {
                                    $this->updateRelations($relative, $relation['relations']);
                                }
                            }
                        }
                    }
                }
                continue;
            }
            if ($query instanceof BelongsTo) {
                if (isset($relation['clean']) && $relation['clean']) {
                    $query->delete();
                    $isCleaned = true;
                }
                if (isset($relation['data']) && $relation['data']) {
                    if ($isCleaned) {
                        $relative = $query->create($relation['data']);
                        $query->associate($relative);
                    } else {
                        $relative = $item->{$relation['name']};

                        if (empty($relative)) {
                            $relative = $query->create($relation['data']);
                            $query->associate($relative);
                        } else {
                            $relative->update($relation['data']);
                        }
                    }
                    if (!empty($relation['relations']) && !empty($relative)) {
                        $this->updateRelations($relative, $relation['relations']);
                    }
                }
                continue;
            }
            if ($query instanceof BelongsToMany) {
                if (isset($relation['sync']) && $relation['sync']) {
                    $query->sync($relation['data']);
                    continue;
                }
                if (isset($relation['clean']) && $relation['clean']) {
                    $query->detach();
                }
                if (isset($relation['data']) && $relation['data']) {
                    $query->attach($relation['data']);
                }
            }
        }
    }

    /**
     * @param $id
     * @param array $relations
     * @return mixed
     * @throws \Exception
     */
    public function deleteWith($id, $relations = [])
    {
        $this->startTransaction();

        try {
            $item = $this->find($id);
            $this->deleteRelations($item, $relations);

            $this->delete($id);

            $this->commitTransaction();

            return $item;
        } catch (\Exception $exception) {
            $this->rollBackTransaction();

            throw $exception;
        }
    }

    /**
     * @param $item
     * @param array $relations
     */
    protected function deleteRelations($item, array $relations)
    {
        foreach ($relations as $relation) {
            $query = $item->{$relation['name']}();

            if ($query instanceof HasOne || $query instanceof MorphOne) {
                $relative = $query->first();

                if (!empty($relation['relations']) && !empty($relative)) {
                    $this->deleteRelations($relative, $relation['relations']);
                }

                if (!empty($relative)) {
                    $relative->delete();
                }

                continue;
            }
            if ($query instanceof HasMany || $query instanceof MorphMany) {
                $relatives = $query->get();

                foreach ($relatives as $relative) {
                    if (!empty($relation['relations'])) {
                        $this->deleteRelations($relative, $relation['relations']);
                    }

                    $relative->delete();
                }

                continue;
            }
            if ($query instanceof BelongsToMany) {
                $query->detach();
            }
        }
    }

    /**
     * @param string $column
     * @param null $key
     * @return array|\Illuminate\Support\Collection
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function pluck($column, $key = null)
    {
        $result = parent::pluck($column, $key);

        $this->resetModel();
        $this->resetScope();

        return $result;
    }

    /**
     * @param array $data
     * @return bool
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function insert(array $data)
    {
        $result = $this->model->newQuery()->insert($data);

        $this->resetModel();
        $this->resetScope();

        return $result;
    }

    /**
     * @param array $attributes
     * @param $id
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function update(array $attributes, $id)
    {
        $this->applyScope();

        $temporarySkipPresenter = $this->skipPresenter;

        $this->skipPresenter(true);

        $model = $this->model->findOrFail($id);
        $model->fill($attributes);

        $this->changes = $model->getDirty();
        $model->save();

        $this->skipPresenter($temporarySkipPresenter);

        $this->resetModel();
        $this->resetScope();

        return $this->parserResult($model);
    }

    /**
     * @param array $data
     * @return bool
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function updateAll(array $data)
    {
        $this->applyCriteria();
        $this->applyScope();

        $result = $this->model->update($data);

        $this->resetModel();
        $this->resetScope();

        return $result;
    }

    /**
     * @return bool|null
     * @throws \Exception
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function deleteAll()
    {
        $this->applyCriteria();
        $this->applyScope();

        $deleted = $this->model->delete();

        $this->resetModel();

        return $deleted;
    }

    /**
     * @return bool|null
     * @throws \Exception
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function forceDeleteAll()
    {
        $this->applyCriteria();
        $this->applyScope();

        $deleted = $this->model->forceDelete();

        $this->resetModel();

        return $deleted;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getModel()
    {
        $this->applyCriteria();
        $this->applyScope();

        return $this->model;
    }

    /**
     * @param $column
     * @param int $step
     * @return int
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function increment($column, $step = 1)
    {
        $this->applyCriteria();
        $this->applyScope();

        $incremented = $this->model->increment($column, $step);

        $this->resetModel();

        return $incremented;
    }

    /**
     * @param $column
     * @param int $step
     * @return int
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function decrement($column, $step = 1)
    {
        $this->applyCriteria();
        $this->applyScope();

        $decremented = $this->model->decrement($column, $step);

        $this->resetModel();

        return $decremented;
    }

    /**
     *
     */
    public function resetChanges()
    {
        $this->changes = [];
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function startTransaction()
    {
        DB::beginTransaction();

        return $this;
    }

    /**
     * @return $this
     */
    public function commitTransaction()
    {
        DB::commit();

        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function rollBackTransaction()
    {
        DB::rollBack();

        return $this;
    }

    /**
     * @return int
     */
    public function transactionLevel()
    {
        return DB::transactionLevel();
    }

    /**
     * @return $this
     */
    public function lockForUpdate()
    {
        $this->model->lockForUpdate();

        return $this;
    }

    /**
     * @param null $attributes
     * @return bool
     */
    public function isDirty($attributes = null)
    {
        foreach (Arr::wrap($attributes) as $attribute) {
            if (array_key_exists($attribute, $this->changes)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $id
     * @param $field
     * @return null
     */
    public function findField($id, $field)
    {
        $model = $this->find($id, [
            $field
        ]);

        if (empty($model)) {
            return null;
        }

        return $model->{$field};
    }

    /**
     * @return mixed
     */
    public function toSql()
    {
        $addSlashes = str_replace('?', "'?'", $this->model->toSql());
        return vsprintf(str_replace('?', '%s', $addSlashes), $this->model->getBindings());
    }

    /**
     * @param $model
     * @param array $relations
     * @return mixed
     */
    public function loadRelations($model, array $relations)
    {
        $rel = [];
        foreach ($relations as $relation => $options) {
            $rel = array_merge($rel, $this->process($relation, $options));
        }
        return $model->load($rel);
    }

    /**
     * @return mixed
     */
    public function cursor()
    {
        $this->applyCriteria();
        $this->applyScope();
        return $this->model->cursor();
    }
}
