<?php

namespace App\Http\Controllers\Core;

use App\Queries\Query;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Collection;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected Request $request;

    protected bool $withQuery = true;

    /** @var class-string<Model> */
    protected string $modelClass;
    protected Query $query;
    protected string $queryClass = Query::class;
    protected Model $model;
    /** @var Collection<Model>|LengthAwarePaginator<Model> */
    protected Collection|LengthAwarePaginator $items;

    /**
     * **Filters**
     */
    protected array $filterable = [];
    protected array $sortable = [];
    protected array $searchable = [];

    protected array $defaultSort = [
        'column' => 'id',
        'direction' => 'desc'
    ];

    /**
     * **Response**
     */
    protected bool $pagination = false;
    protected int $perPage = 20;

    /** @var class-string<JsonResource> */
    protected string $modelResource;

    /** @var class-string<JsonResource> */
    protected string $itemsResource;

    public function __construct(Request $request)
    {
        $this->request = $request;

        if ($this->withQuery) {
            $this->newQuery();
        }
    }

    protected function setModel(Model $model): static
    {
        $this->query->setModel($model);
        $this->model = $model;
        return $this;
    }

    protected function newQuery(Query|Builder $query = null): Query
    {
        if ($query instanceof Builder) {
            $query = new $this->queryClass($query);
        } elseif (empty($query)) {
            $query = new $this->queryClass($this->modelClass::query());
        }

        $this->query = $query;

        return $this->query;
    }

    /**
     * Load items with filter, searching, sorting and pagination(if it's enabled)
     */
    protected function loadItems($columns = ['*']): static
    {
        $this->query->filter($this->request->input('filters', []), $this->filterable)
            ->sort(
                $this->request->input('sort.column', $this->defaultSort['column'],),
                $this->request->input('sort.direction', $this->defaultSort['direction']),
                $this->sortable
            )->search(
                $this->request->input('search', []), $this->searchable
            );

        if ($this->pagination) {
            $this->items = $this->query->builder()->paginate(
                $this->request->input('per-page', $this->perPage), $columns, 'page', $this->request->input('page')
            );
        } else {
            $this->items = $this->query->builder()->get($columns);
        }

        return $this;
    }

    /**
     * Makes a JSON response by $model. Uses $modelResource if it exists
     */
    protected function respondWithModel(): JsonResponse
    {
        if (!empty($this->modelResource)) {
            $this->modelResource::withoutWrapping();
            return (new $this->modelResource($this->model))->response()->setStatusCode(200);
        }

        return response()->json($this->model);
    }

    /**
     * Makes a JSON response by $model. Uses $itemResource if it exists
     */
    protected function respondWithItems(): JsonResponse
    {
        if (!empty($this->itemsResource)) {
            $this->itemsResource::withoutWrapping();
            return $this->itemsResource::collection($this->items)->response()->setStatusCode(200);
        }

        return response()->json($this->items);
    }
}
