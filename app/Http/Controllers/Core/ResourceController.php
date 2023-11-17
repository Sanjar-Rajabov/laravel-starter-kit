<?php

namespace App\Http\Controllers\Core;

use App\Helpers\ResponseHelper;
use App\Http\Requests\Contracts\CreateFormRequestInterface;
use App\Http\Requests\Contracts\PaginationFormRequestInterface;
use App\Http\Requests\Contracts\UpdateFormRequestInterface;
use App\Http\Requests\Core\PaginationRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class ResourceController extends Controller
{
    protected string $paginationFormRequest = PaginationRequest::class;
    protected string $createFormRequest;
    protected string $updateFormRequest;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        app()->bind(PaginationFormRequestInterface::class, $this->paginationFormRequest);
        if (!empty($this->createFormRequest)) {
            app()->bind(CreateFormRequestInterface::class, $this->createFormRequest);
        }
        if (!empty($this->updateFormRequest)) {
            app()->bind(UpdateFormRequestInterface::class, $this->updateFormRequest);
        }
    }

    public function index(): JsonResponse
    {
        $this->query->builder()->with($this->relationsForIndex());
        $this->loadItems();

        return ResponseHelper::items($this->items, $this->itemsResource ?? null);
    }

    public function show($id): JsonResponse
    {
        $this->query->builder()->with($this->relationsForShow());
        $this->setModel($this->query->find($id));
        return ResponseHelper::model($this->model, $this->modelResource ?? null);
    }

    /**
     * @throws Exception
     */
    public function create(CreateFormRequestInterface $request): JsonResponse
    {
        $this->query->setModel(new $this->modelClass);
        $this->query->save($request->safe()->all());
        return ResponseHelper::created();
    }

    /**
     * @throws Exception
     */
    public function update(UpdateFormRequestInterface $request, int $id): JsonResponse
    {
        $this->query->find($id);
        $this->query->save($request->safe()->all());
        return ResponseHelper::updated();
    }

    public function delete(int $id): JsonResponse
    {
        $this->query->find($id);
        $this->query->delete();
        return ResponseHelper::deleted();
    }

    protected function relationsForIndex(): array
    {
        return [];
    }

    protected function relationsForShow(): array
    {
        return [];
    }
}
