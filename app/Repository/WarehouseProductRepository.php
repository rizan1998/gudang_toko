<?<?php

    namespace App\Repository;

    use App\Models\WarehouseProduct;
    use Illuminate\Validation\ValidationException;

    class WarehouseProductRepository
    {

        public function getByWarehouseAndProduct(int $warehouseId, int $fields = ['*']): ?WarehouseProduct
        {
            return \App\Models\WarehouseProduct::where('warehouse_id', $warehouseId)
                ->select($fields)
                ->with(['product.category'])
                ->get();
        }

        public function updateStock($warehouseId, $productId, $stock): WarehouseProduct
        {
            $warehouseProduct = $this->getByWarehouseAndProduct($warehouseId, $productId);

            if (!$warehouseProduct) {
                throw ValidationException::withMessages(['message' => 'Product not found in the specified warehouse.']);
            }

            $warehouseProduct->update(['stock' => $stock]);
            return $warehouseProduct;
        }
    }
