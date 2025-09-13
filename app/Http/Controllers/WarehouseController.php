<?php

namespace App\Http\Controllers;


use App\Http\Requests\WarehouseRequest;
use App\Http\Resources\WarehouseResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Services\WarehouseService;

class WarehouseController extends Controller
{
    private WarehouseService $warehouseService;

    public function __construct(WarehouseService $warehouseService)
    {
        $this->warehouseService = $warehouseService;
    }

    public function index()
    {
        $fields = ['id', 'name', 'photo', 'tagline'];
        $categories = $this->warehouseService->getAll($fields);
        return response()->json(WarehouseResource::collection($categories), 200);
    }

    public function show(int $id)
    {
        try {
            $fields = ['id', 'name', 'photo', 'tagline'];
            $Warehouse = $this->warehouseService->getById($id, $fields);
            return response()->json(new WarehouseResource($Warehouse), 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

    public function store(WarehouseRequest $request)
    {
        $warehouse = $this->warehouseService->create($request->validated());
        return response()->json(new warehouseResource($warehouse), 201);
    }

    public function update(WarehouseRequest $request, int $id)
    {
        try {
            $warehouse = $this->warehouseService->update($id, $request->validated());
            return response()->json(new warehouseResource($warehouse), 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->warehouseService->delete($id);
            return response()->json(['message' => 'Warehouse deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }
}
