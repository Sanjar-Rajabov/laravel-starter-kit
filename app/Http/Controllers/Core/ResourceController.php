<?php

namespace App\Http\Controllers\Core;

use App\Enums\SortTypes;
use App\Helpers\ResponseHelper;
use App\Http\Requests\Core\DeleteByIdRequest;
use App\Http\Requests\Core\GetByIdRequest;
use App\Http\Requests\Core\Interfaces\CreateRequestInterface;
use App\Http\Requests\Core\Interfaces\DeleteRequestInterface;
use App\Http\Requests\Core\Interfaces\GetAllRequestInterface;
use App\Http\Requests\Core\Interfaces\GetOneRequestInterface;
use App\Http\Requests\Core\Interfaces\UpdateRequestInterface;
use App\Http\Requests\Core\PaginationRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class ResourceController extends Controller
{
    protected bool $withQuery = true;
    protected string $getAllRequest = PaginationRequest::class;
    protected string $getOneRequest = GetByIdRequest::class;
    protected string $createRequest;
    protected string $updateRequest;
    protected string $deleteRequest = DeleteByIdRequest::class;

    protected array $sortable = [
        'id' => SortTypes::class
    ];

    protected bool $pagination = true;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        app()->bind(GetAllRequestInterface::class, $this->getAllRequest);
        app()->bind(GetOneRequestInterface::class, $this->getOneRequest);
        app()->bind(DeleteRequestInterface::class, $this->deleteRequest);

        if (!empty($this->createRequest)) {
            app()->bind(CreateRequestInterface::class, $this->createRequest);
        }
        if (!empty($this->updateRequest)) {
            app()->bind(UpdateRequestInterface::class, $this->updateRequest);
        }
    }

    public function getAll(GetAllRequestInterface $request): JsonResponse
    {
        $this->loadItems();

        return ResponseHelper::items($this->items, $this->itemsResource ?? null);
    }

    public function getOne(GetOneRequestInterface $request): JsonResponse
    {
        $this->setModel($this->query->find($request->route('id')));
        return ResponseHelper::model($this->model, $this->modelResource ?? null);
    }

    /**
     * @throws Exception
     */
    public function create(CreateRequestInterface $request): JsonResponse
    {
        $this->query->setModel(new $this->modelClass);
        $this->query->save($request->safe()->all());
        return ResponseHelper::created();
    }

    /**
     * @throws Exception
     */
    public function update(UpdateRequestInterface $request): JsonResponse
    {
        $model = $this->query->find($request->route('id'));
        $this->query->save($request->safe()->all());
        return ResponseHelper::updated();
    }

    public function delete(DeleteRequestInterface $request): JsonResponse
    {
        $this->query->find($request->route('id'));
        $this->query->delete();
        return ResponseHelper::deleted();
    }
}
