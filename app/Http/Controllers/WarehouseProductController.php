<?php

namespace App\Http\Controllers;

use App\Http\Requests\WarehouseProductRequest;
use Illuminate\Http\Request;
use App\Services\WarehouseService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class WarehouseProductController extends Controller
{
    private WarehouseService $warehouseService;

    public function __construct(WarehouseService $warehouseService)
    {
        $this->warehouseService = $warehouseService;
    }

    public function attach(Request $request, int $warehouseId)
    {

        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'stock' => 'required|integer|min:0',
        ]);

        try {
            $warehouseProduct = $this->warehouseService->attachProducts(
                $warehouseId,
                $request->product_id,
                $request->stock
            );

            return response()->json($warehouseProduct, 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

    public function detach(int $warehouseId, int $productId)
    {
        $this->warehouseService->detachProducts($warehouseId, $productId);
        return response()->json(['message' => 'Product detached from warehouse successfully'], 200);
    }

    public function update(WarehouseProductRequest $request, int $warehouseId, int $productId)
    {
        try {
            $warehouseProduct = $this->warehouseService->updateProductStock(
                $warehouseId,
                $productId,
                $request->validated()['stock']
            );

            return response()->json($warehouseProduct, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }
}
