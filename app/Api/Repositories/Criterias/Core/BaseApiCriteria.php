<?php
namespace Api\Repositories\Criterias\Core;

use Api\Repositories\BaseApiRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Prettus\Repository\Contracts\CriteriaInterface;

/**
 * Class BaseApiCriteria
 * @package Api\Repositories\Criterias\Core
 */
abstract class BaseApiCriteria implements CriteriaInterface
{
    /**
     * @var BaseApiRepository
     */
    protected $repository;

    /**
     * @param Builder $query
     * @return string
     */
    protected function getTable($query)
    {
        return $query->getModel()->getTable();
    }

    /**
     * @param Builder $query
     * @param $relation
     * @return string
     */
    protected function getRelationTable($query, $relation)
    {
        /**
         * @var $relation Relation
         */
        $relation = $query->getModel()->{$relation}();

        return $relation->getRelated()->getTable();
    }

    /**
     * @param Builder $query
     * @param array $options
     */
    protected function buildData(&$query, array $options)
    {
        if (Arr::get($options, 'selects')) {
            $this->buildSelects($query, $options['selects']);
        }
        if (Arr::get($options, 'selectsRaw')) {
            $this->buildSelectsRaw($query, $options['selectsRaw']);
        }
        if (Arr::get($options, 'where')) {
            $this->buildWhere($query, $options['where']);
        }
        if (Arr::get($options, 'orWhere')) {
            $this->buildWhere($query, $options['orWhere'], true);
        }
        if (Arr::get($options, 'whereNested')) {
            $this->buildWhereNested($query, $options['whereNested']);
        }
        if (Arr::get($options, 'orWhereNested')) {
            $this->buildWhereNested($query, $options['orWhereNested'], true);
        }
        if (Arr::get($options, 'whereIn')) {
            $this->buildWhereIn($query, $options['whereIn']);
        }
        if (Arr::get($options, 'whereIntegerInRaw')) {
            $this->buildWhereIntegerInRaw($query, $options['whereIntegerInRaw']);
        }
        if (Arr::get($options, 'orWhereIn')) {
            $this->buildWhereIn($query, $options['orWhereIn'], true);
        }
        if (Arr::get($options, 'whereNotIn')) {
            $this->buildWhereNotIn($query, $options['whereNotIn']);
        }
        if (Arr::get($options, 'orWhereNotIn')) {
            $this->buildWhereNotIn($query, $options['whereNotIn'], true);
        }
        if (Arr::get($options, 'whereNull')) {
            $this->buildWhereNull($query, $options['whereNull']);
        }
        if (Arr::get($options, 'whereNotNull')) {
            $this->buildWhereNull($query, $options['whereNotNull'], false, true);
        }
        if (Arr::get($options, 'orWhereNull')) {
            $this->buildWhereNull($query, $options['orWhereNull'], true);
        }
        if (Arr::get($options, 'orWhereNotNull')) {
            $this->buildWhereNull($query, $options['orWhereNotNull'], true, true);
        }
        if (Arr::get($options, 'has')) {
            $this->buildHas($query, $options['has']);
        }
        if (Arr::get($options, 'whereHas')) {
            $this->buildWhereHas($query, $options['whereHas']);
        }
        if (Arr::get($options, 'whereDoesntHave')) {
            $this->buildWhereHas($query, $options['whereDoesntHave'], true);
        }
        if (Arr::get($options, 'whereHasMorph')) {
            $this->buildWhereHasMorph($query, $options['whereHasMorph']);
        }
        if (Arr::get($options, 'having')) {
            $this->buildHaving($query, $options['having']);
        }
        if (Arr::get($options, 'havingRaw')) {
            $this->buildHavingRaw($query, $options['havingRaw']);
        }
        if (Arr::get($options, 'groupBy')) {
            $this->buildGroupBy($query, $options['groupBy']);
        }
        if (Arr::get($options, 'sortBy')) {
            $this->buildSortBy($query, $options['sortBy'], Arr::get($options, 'order'));
        }
        if (Arr::get($options, 'criteria')) {
            $this->buildCriteria($query, $options['criteria']);
        }
        if (Arr::get($options, 'relations')) {
            $this->buildRelations($query, $options['relations']);
        }
        if (Arr::get($options, 'join')) {
            $this->buildJoin($query, $options['join']);
        }
        if (Arr::get($options, 'leftJoin')) {
            $this->buildJoin($query, $options['leftJoin'], 'leftJoin');
        }
        if (Arr::get($options, 'rightJoin')) {
            $this->buildJoin($query, $options['rightJoin'], 'rightJoin');
        }
        if (Arr::get($options, 'limit')) {
            $this->buildLimit($query, $options['limit']);
        }
    }

    /**
     * @param Builder $query
     * @param $having
     */
    protected function buildHaving(&$query, $having)
    {
        $query = $query->having($having);
    }

    /**
     * @param Builder $query
     * @param $selects
     */
    protected function buildSelects(&$query, $selects)
    {
        $query = $query->select($selects);
    }

    /**
     * @param Builder $query
     * @param $selectsRaw
     */
    protected function buildSelectsRaw(&$query, $selectsRaw)
    {
        foreach ($selectsRaw as $selectRaw) {
            $query = $query->selectRaw($selectRaw);
        }
    }

    /**
     * @param Builder $query
     * @param $groupBy
     */
    protected function buildGroupBy(&$query, $groupBy)
    {
        if (is_array($groupBy)) {
            $query = $query->groupBy(...$groupBy);
        } else {
            $query = $query->groupBy($groupBy);
        }
    }

    /**
     * @param Builder $query
     * @param $havingRaw
     */
    protected function buildHavingRaw(&$query, $havingRaw)
    {
        $query = $query->havingRaw($havingRaw);
    }

    /**
     * @param Builder $query
     * @param $sortBy
     * @param string $order
     */
    protected function buildSortBy(&$query, $sortBy, $order = 'asc')
    {
        $query = $query->orderBy($sortBy, $order);
    }

    /**
     * @param Builder $query
     * @param $has
     */
    protected function buildHas(&$query, $has)
    {
        foreach ($has as $hasOptions) {
            $query = $query->has($hasOptions['relation']);
        }
    }

    /**
     * @param $query
     * @param $whereIn
     * @param bool $or
     */
    protected function buildWhereIn(&$query, $whereIn, $or = false)
    {
        $method = $or ? 'orWhereIn' : 'whereIn';

        foreach ($whereIn as $whereInOptions) {
            $query = $query->{$method}($whereInOptions['field'], $whereInOptions['value']);
        }
    }

    /**
     * @param $query
     * @param $whereNotIn
     * @param false $or
     */
    protected function buildWhereNotIn(&$query, $whereNotIn, $or = false)
    {
        $method = $or ? 'orWhereNotIn' : 'whereNotIn';

        foreach ($whereNotIn as $whereNotInOptions) {
            $query = $query->{$method}($whereNotInOptions['field'], $whereNotInOptions['value']);
        }
    }

    /**
     * @param Builder $query
     * @param $whereIn
     */
    protected function buildWhereIntegerInRaw(&$query, $whereIn)
    {
        foreach ($whereIn as $whereInRawOptions) {
            $query = $query->whereIntegerInRaw($whereInRawOptions['field'], $whereInRawOptions['value']);
        }
    }

    /**
     * @param $query
     * @param $whereNull
     * @param bool $or
     * @param bool $reverse
     */
    protected function buildWhereNull(&$query, $whereNull, $or = false, $reverse = false)
    {
        $method = $reverse ? 'whereNotNull' : 'whereNull';

        if ($or) {
            $method = 'or' . ucfirst($method);
        }

        foreach ($whereNull as $whereNullOptions) {
            $query = $query->{$method}($whereNullOptions['field']);
        }
    }

    /**
     * @param Builder $query
     * @param $where
     * @param bool $or
     */
    protected function buildWhere(&$query, $where, $or = false)
    {
        $method = $or ? 'orWhere' : 'where';

        $query = $query->{$method}($where);
    }

    /**
     * @param Builder $query
     * @param $criteria
     */
    protected function buildCriteria(&$query, $criteria)
    {
        foreach ($criteria as $item) {
            if ($item instanceof BaseApiCriteria) {
                $query = $item->apply($query, $this->repository);
            }
        }
    }

    /**
     * @param Builder $query
     * @param $whereNested
     * @param bool $or
     */
    protected function buildWhereNested(&$query, $whereNested, $or = false)
    {
        $method = $or ? 'orWhere' : 'where';

        foreach ($whereNested as $whereNestedOptions) {
            $query = $query->{$method}(function (Builder $nestedQuery) use ($whereNestedOptions) {
                $this->buildData($nestedQuery, $whereNestedOptions);
            });
        }
    }

    /**
     * @param $query
     * @param $whereHas
     * @param false $reverse
     */
    protected function buildWhereHas(&$query, $whereHas, $reverse = false)
    {
        $method = $reverse ? 'whereDoesntHave' : 'whereHas';

        foreach ($whereHas as $whereHasOptions) {
            $query = $query->{$method}($whereHasOptions['relation'], function (Builder $relatedQuery) use ($whereHasOptions) {
                $this->buildData($relatedQuery, $whereHasOptions);
            });
        }
    }

    /**
     * @param Builder $query
     * @param $whereHasMorph
     */
    protected function buildWhereHasMorph(&$query, $whereHasMorph)
    {
        foreach ($whereHasMorph as $whereHasMorphOptions) {
            if ($whereHasMorphOptions['types'] !== '*') {
                if (!is_array($whereHasMorphOptions['types'])) {
                    $whereHasMorphOptions['types'] = [$whereHasMorphOptions['types']];
                }
                foreach ($whereHasMorphOptions['types'] as &$type) {
                    $type = Relation::getMorphedModel($type);
                }
            }

            $query = $query->whereHasMorph($whereHasMorphOptions['relation'], $whereHasMorphOptions['types'], function (Builder $relatedQuery) use ($whereHasMorphOptions) {
                $this->buildData($relatedQuery, $whereHasMorphOptions);
            });
        }
    }

    /**
     * @param Builder $query
     * @param $relations
     */
    protected function buildRelations(&$query, $relations)
    {
        $result = [];

        foreach ($relations as $relation => $options) {
            $result[$relation] = function(Relation $relatedQuery) use ($options) {
                $this->buildData($relatedQuery, $options);

                if ($relatedQuery instanceof BelongsTo) {
                    $relatedQuery->withDefault();
                }
            };
        }

        $query = $query->with($result);
    }

    /**
     * @param Builder $query
     * @param $innerJoin
     * @param string $method
     */
    protected function buildJoin(&$query, $innerJoin, $method = 'join')
    {
        foreach ($innerJoin as $join) {
            if (!is_array(Arr::get($join, 'on.0'))) {
                $join['on'] = [$join['on']];
            }

            $query = $query->{$method}($join['table'], function ($joinQuery) use ($join) {
                foreach ($join['on'] as $onOptions) {
                    $joinMethod = Arr::get($onOptions, 'isOr') === true ? 'orOn' : 'on';
                    $joinQuery->{$joinMethod}($onOptions['local'], $onOptions['comparison'], $onOptions['foreign']);
                }
            });
        }
    }

    /**
     * @param $query
     * @param $limit
     */
    protected function buildLimit(&$query, $limit)
    {
        $query = $query->limit($limit);
    }
}
