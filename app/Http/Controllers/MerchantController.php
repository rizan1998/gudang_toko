<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MerchantService;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\MerchantRequest;
use App\Http\Resources\MerchantResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MerchantController extends Controller
{
    private MerchantService $merchantService;

    public function __construct(MerchantService $merchantService)
    {
        $this->merchantService = $merchantService;
    }

    public function index()
    {
        $fields = ['id', 'name', 'photo', 'tagline'];
        $merchants = $this->merchantService->getAll($fields);
        return response()->json(MerchantResource::collection($merchants), 200);
    }

    public function show(int $id)
    {
        try {
            $fields = ['id', 'name', 'photo', 'tagline'];
            $merchant = $this->merchantService->getById($id, $fields);
            return response()->json(new MerchantResource($merchant), 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

    public function store(MerchantRequest $request)
    {
        $merchant = $this->merchantService->create($request->validated());
        return response()->json(new MerchantResource($merchant), 201);
    }

    public function destroy(int $id)
    {
        try {
            $this->merchantService->delete($id);
            return response()->json(['message' => 'Merchant deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

    public function getMyMerchantProfile(int $userId)
    {
        $userId = Auth::id($userId);

        try {
            $merchant = $this->merchantService->getByKeeperId($userId, ['*']);
            return response()->json(new MerchantResource($merchant), 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }
}
