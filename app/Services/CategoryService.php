<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use App\Repository\CategoryRepository;
use Illuminate\Support\Facades\Storage;

class CategoryService
{
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getAll(array $fields)
    {
        return $this->categoryRepository->getAll($fields);
    }

    public function getById($id, array $fields)
    {
        return $this->categoryRepository->getById($id, $fields ?? ['*']);
    }

    public function create(array $data)
    {
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }
    }

    private function uploadPhoto(UploadedFile $photo)
    {
        $path = $photo->store('categories', 'public');
        return $path;
    }

    public function update($id, array $data)
    {
        $fileds = ['id', 'photo'];
        $category = $this->categoryRepository->getById($id, $fileds);
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }
        $category->update($data);
        return $category;
    }

    private function deletePhoto($photoPath)
    {
        $relativePath = 'categories/' . basename($photoPath);
        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
    }

    public function delete(int $id)
    {
        $fields = ['id', 'photo'];
        $category = $this->categoryRepository->getById($id, $fields);
        if ($category->photo) {
            $this->deletePhoto($category->photo);
        }
        return $this->categoryRepository->delete($id);
    }
}
