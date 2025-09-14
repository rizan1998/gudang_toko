<?<?php

    namespace App\Repository;

    use App\Models\MerchantProduct;
    use Illuminate\Database\Eloquent\Collection;
    use Illuminate\Validation\ValidationException;

    class MerchantProductRepository
    {


        public function create(array $data): MerchantProduct
        {
            return MerchantProduct::create($data);
        }

        public function getByMerchantAndProduct(int $merchantId, int $productId): ?MerchantProduct
        {
            return MerchantProduct::where('merchant_id', $merchantId)
                ->where('product_id', $productId)
                ->first();
        }

        public function updateStock(int $merchantId, int $productId, int $stock): MerchantProduct
        {
            $merchantProduct = $this->getByMerchantAndProduct($merchantId, $productId);

            if (!$merchantProduct) {
                throw ValidationException::withMessages(['error' => 'MerchantProduct not found']);
            }

            $merchantProduct->update(['stock' => $stock]);
            return $merchantProduct;
        }
    }


    ?>