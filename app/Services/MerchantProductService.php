<?php

namespace App\Services;

use App\Repository\MerchantProductRepository;
use App\Repository\WarehouseProductRepository;
use Illuminate\Support\Facades\DB;


use App\Models\WarehouseProduct;
use Illuminate\Validation\ValidationException;

class MerchantProductService
{

    private MerchantProductRepository $merchantProductRepository;
    private WarehouseProductRepository $warehouseProductRepository;

    public function __construct(
        MerchantProductRepository $merchantProductRepository,
        WarehouseProductRepository $warehouseProductRepository
    ) {
        $this->merchantProductRepository = $merchantProductRepository;
        $this->warehouseProductRepository = $warehouseProductRepository;
    }

    public function assignProductToMerchant(array $data)
    {
        return DB::transaction(function () use ($data) {

            $warehouseProduct = $this->warehouseProductRepository->getByWarehouseAndProduct($data['warehouse_id'], $data['product_id']);

            if (!$warehouseProduct || $warehouseProduct->stock < $data['stock']) {
                throw ValidationException::withMessages(['error' => 'Insufficient stock in warehouse or product not found']);
            }

            $existingMerchantProduct = $this->merchantProductRepository->getByMerchantAndProduct($data['merchant_id'], $data['product_id']);

            if ($existingMerchantProduct) {
                throw ValidationException::withMessages(['product' => 'Product already exists in this merchant']);
            }

            $this->warehouseProductRepository->updateStock($data['warehouse_id'], $data['product_id'], $warehouseProduct->stock - $data['stock']);


            return $this->merchantProductRepository->create([
                'warehouse_id' => $data['warehouse_id'],
                'merchant_id' => $data['merchant_id'],
                'product_id' => $data['product_id'],
                'stock' => $data['stock']
            ]);
        });
    }
}
