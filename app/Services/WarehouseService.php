<?php

namespace App\Services;

use App\Repository\WarehouseRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class WarehouseService
{
    private   $warehouseRepository;

    public function __construct(WarehouseRepository $warehouseRepository)
    {
        $this->warehouseRepository = $warehouseRepository;
    }

    public function getAll(array $fields)
    {
        return $this->warehouseRepository->getAll($fields);
    }

    public function getById($id, array $fields)
    {
        return $this->warehouseRepository->getById($id, $fields ?? ['*']);
    }

    public function create(array $data)
    {
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }
    }

    private function uploadPhoto(UploadedFile $photo)
    {
        $path = $photo->store('warehouses', 'public');
        return $path;
    }

    public function update($id, array $data)
    {
        $fileds = ['id', 'photo'];
        $warehouse = $this->warehouseRepository->getById($id, $fileds);
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }
        $warehouse->update($data);
        return $warehouse;
    }

    private function deletePhoto($photoPath)
    {
        $relativePath = 'warehouses/' . basename($photoPath);
        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
    }

    public function attachProducts(int $warehouseId, int $productId, int $stock)
    {
        // $warehouse = $this->warehouseRepository->getById($warehouseId, ['id']);
        // $warehouse->products()->attach($productId, ['stock' => $stock]);
        // return $warehouse->load('products');

        $warehouse = $this->warehouseRepository->getById($warehouseId, ['id']);
        $warehouse->products()->syncWithoutDetaching([$productId => ['stock' => $stock]]);
    }

    public function detachProducts(int $warehouseId, int $productId)
    {
        // $warehouse = $this->warehouseRepository->getById($warehouseId, ['id']);
        // $warehouse->products()->detach($productId);
        // return $warehouse->load('products');

        $warehouse = $this->warehouseRepository->getById($warehouseId, ['id']);
        $warehouse->products()->detach($productId);
    }

    public function updateProductStock(int $warehouseId, int $productId, int $stock)
    {
        $warehouse = $this->warehouseRepository->getById($warehouseId, ['id']);
        $warehouse->products()->updateExistingPivot($productId, ['stock' => $stock]);
        // return $warehouse->load('products');
        return $warehouse->products()->where('product_id', $productId)->first();
    }

    public function delete(int $id)
    {
        $fileds = ['id'];
        $warehouse = $this->warehouseRepository->getById($id, $fileds);
        if ($warehouse->photo) {
            $this->deletePhoto($warehouse->photo);
        }
        return $this->warehouseRepository->delete($id);
    }
}
