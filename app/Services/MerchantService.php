<?php

namespace App\Services;

use App\Repository\MerchantRepository;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MerchantService
{

    private MerchantRepository $merchantRepository;
    public function __construct(MerchantRepository $merchantRepository)
    {
        $this->merchantRepository = $merchantRepository;
    }

    public function getAll($fields)
    {
        return $this->merchantRepository->getAll($fields);
    }

    public function getById($id, $fields)
    {
        return $this->merchantRepository->getById($id, $fields ?? ['*']);
    }

    private function uploadPhoto($photo)
    {
        $relativePath = 'merchants/' . basename($photo);
        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
    }

    public function create(array $data)
    {
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }

        return $this->merchantRepository->create($data);
    }
    public function update($id, array $data)
    {
        $fields = ["*"];
        $merchant = $this->merchantRepository->getById($id, $fields);

        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            if (!empty($merchant->photo)) {
                $this->uploadPhoto($merchant->photo);
            }
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }
        return $this->merchantRepository->update($id, $data);
    }
    public function delete($id)
    {
        $fields = ['*'];
        $merchant = $this->merchantRepository->getById($id, $fields);
        if (!empty($merchant->photo)) {
            $this->deletePhoto($merchant->photo);
        }
        return $this->merchantRepository->delete($id);
    }

    private function deletePhoto($photoPath)
    {
        $relativePath = 'merchants/' . basename($photoPath);
        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
    }

    public function getByUserId($userId, $fields)
    {
        return $this->merchantRepository->getByUserId($userId, $fields ?? ['*']);
    }

    public function getByKeeperId($keeperId, $fields)
    {
        return $this->merchantRepository->getByKeeperId($keeperId, $fields ?? ['*']);
    }
}
