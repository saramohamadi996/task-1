<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Product\StoreProductRequest;
use App\Http\Resources\Api\V1\Product\ProductCollection;
use App\Models\Article\Article;
use App\Models\Product\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display based on highest inventory and highest profit
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->apiResponse(
            new ProductCollection(
                Product::orderBy(DB::raw("price * available_stock"), 'desc')
                    ->get()
            )
        );
    }

    /**
     * @param StoreProductRequest $request
     * @return JsonResponse
     * @throws \Exception|\Throwable
     */
    public function storeJson(StoreProductRequest $request): JsonResponse
    {
        $availableStock = 0;
        $list = $this->getJsonFile($request->file, 'products');
        //todo add Db Transaction
        foreach ($list as $data) {
            $product = Product::create($data);
            if ($product) {
                $newArticles = array_map(function ($val) use ($product, &$availableStock) {
                    $val['product_id'] = $product->id;
                    $val['article_id'] = $val['id'];
                    $this->checkAvailabilityOfArticles($val);
                    $availableStock += $val['amount'];
                    unset($val['id']);
                    return $val;
                }, $data['articles']);
                $product->articles()->sync($newArticles);
                $this->storeAvailableStock($product, $availableStock);
            }
        }
        return $this->apiResponse(true);
    }

    /**
     * Checking the availability of the article will return an error if it is not available
     * @param array $val
     * @return void
     * @throws \Throwable
     */
    private function checkAvailabilityOfArticles(array $val): void
    {
        $article = Article::where('id', $val['article_id'])->first();
        throw_if(
            $article->stock < $val['amount'],
            new \Exception("error Stock"));
        $article->decrement('stock', $val['amount']);
    }

    /**
     * @param Product $product
     * @param int $availableStock
     * @return void
     */
    private function storeAvailableStock(Product $product, int $availableStock): void
    {
        $product->available_stock = $availableStock;
        $product->save();
    }
}
