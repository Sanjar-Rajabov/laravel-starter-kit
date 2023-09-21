<?php

namespace App\Http\Controllers;

use App\Http\Requests\Contracts\CreateFormRequestInterface;
use App\Http\Requests\Contracts\PaginationFormRequestInterface;
use App\Http\Requests\Contracts\UpdateFormRequestInterface;
use App\Http\Requests\Core\PaginationRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

abstract class ResourceController extends Controller
{
    protected string $paginationFormRequest = PaginationRequest::class;
    protected string $createFormRequest;
    protected string $updateFormRequest;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        app()->bind(PaginationFormRequestInterface::class, $this->paginationFormRequest);
        app()->bind(CreateFormRequestInterface::class, $this->createFormRequest);
        app()->bind(UpdateFormRequestInterface::class, $this->updateFormRequest);
    }

    public function index(): JsonResponse
    {
        $this->query->builder()->with($this->relationsForIndex());
        return $this->loadItems()->respondWithItems();
    }

    public function show(int $id): JsonResponse
    {
        $this->query->builder()->with($this->relationsForShow());
        return $this->setModel($this->query->find($id))->respondWithModel();
    }

    /**
     * @throws Exception
     */
    public function create(CreateFormRequestInterface $request): Response
    {
        $this->query->setModel(new $this->modelClass);
        $this->query->save($request->safe()->all());
        return response('', 201);
    }

    /**
     * @throws Exception
     */
    public function update(UpdateFormRequestInterface $request, int $id): Response
    {
        $this->query->find($id);
        $this->query->save($request->safe()->all());
        return response('', 200);
    }

    public function delete(int $id): Response
    {
        $this->query->find($id);
        $this->query->delete();
        return response('', 204);
    }

    private function loadRelations(string $method)
    {
        $relations = call_user_func([$this, $method]);

        if (!empty($relations)) {
            $this->query->builder()->with($relations);
        }
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
