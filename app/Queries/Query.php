<?php

namespace App\Queries;

use App\Enums\FilterEnum;
use App\Enums\SortEnum;
use App\Helpers\FilterHelper;
use App\Traits\Language;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Query
{
    use Language;

    protected Builder $query;

    protected Model $model;

    public function __construct(Builder|Model $query)
    {
        $this->newQuery($query);
    }

    public function newQuery(Builder|Model $query): Builder
    {
        if ($query instanceof Builder) {
            $this->query = $query;
        } else {
            $this->query = $query->newQuery();
        }

        return $this->query;
    }

    public function setModel(Model $model): void
    {
        $this->model = $model;
    }

    public function builder(): Builder
    {
        return $this->query;
    }

    public function filter(array $filters, array $options): static
    {
        foreach ($filters as $column => $value) {
            if ((!$type = $options[$column] ?? null) || !isset($value)) {
                continue;
            }

            $param = explode('.', $column);

            if (count($param) == 1) {
                $this->applyFilters($this->query, $type, $column, $value);
            } else {
                $lastParam = array_pop($param);
                $this->query->whereHas(implode('.', $param), function ($query) use ($lastParam, $type, $value) {
                    $this->applyFilters($query, $type, $lastParam, $value);
                });
            }
        }

        return $this;
    }

    public function sort(string $column, string $direction, array $sortable): static
    {
        if (!$type = $sortable[$column] ?? null or !in_array($direction, ['asc', 'desc', 'ASC', 'DESC'])) {
            return $this;
        }

        if ($type === SortEnum::Localized) {
            $column = "$column->{$this->lang()}";
        }

        $this->query->orderBy($column, $direction);

        return $this;
    }

    public function search(mixed $value, array $options): static
    {
        if (empty($value)) {
            return $this;
        }

        $this->query->where(function (Builder $query) use ($value, $options) {
            $this->applySearch($query, $value, $options);
        });

        return $this;
    }

    public function find(int $id, bool $exception = true): Model
    {
        $this->setModel(
            $exception
                ? $this->query->findOrFail($id)
                : $this->query->find($id)
        );

        return $this->model;
    }

    /**
     * @throws Exception
     */
    public function save(array $attributes): void
    {
        DB::beginTransaction();

        try {
            $this->model->fillAndSave($attributes);
            $this->model->deleteRelations($attributes);
            $this->model->saveRelations($attributes);
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        DB::commit();
    }


    public function delete(): void
    {
        $deleteMode = config('app.delete_mode');

        if ($deleteMode === 'delete') {
            $this->model->delete();
        } elseif ($deleteMode === 'deactivate') {
            $this->model->update([
                'active' => !$this->model->active
            ]);
        }
    }

    protected function applyFilters($query, $type, $column, $value): void
    {
        if (!in_array($type, [FilterEnum::In, FilterEnum::Between])) {
            $value = is_array($value) ? implode('', $value) : $value;
        } else {
            if (!is_array($value)) {
                return;
            }
            if ($type == FilterEnum::Between && !is_numeric($value[0])) {
                $value = array_map(fn($date) => Carbon::make($date), $value);
            }
        }
        call_user_func([FilterHelper::class, $type->value], $query, $column, $value);
    }

    protected function applySearch(Builder $query, $value, $options): void
    {
        foreach ($options as $column => $type) {
            $items = explode('.', $column);

            if (count($items) == 1) {
                call_user_func([FilterHelper::class, $type->value], $query, $column, $value);
            } else {
                $query->orWhereHas(
                    $items[0],
                    fn(Builder $query) => $query->where(
                        fn($query) => call_user_func([FilterHelper::class, $type->value], $query, array_pop($items), $value),
                    )
                );
            }
        }
    }

}
