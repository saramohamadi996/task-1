<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\ProductCollection;
use App\Models\Article;
use App\Models\Product;
use Illuminate\Http\Request;


class ProductsController extends Controller
{
    public function index()
    {
        $products = Product::select(['id', 'name', 'price', 'quantity', 'benefit'])
            ->with('articles', function ($article) {
                $article->select('articles.id', 'amount');
            })->get()->sortBy([
                ['benefit', 'desc'],
            ]);
        return new ProductCollection($products);
    }

    public function import(Request $request)
    {
        $json_data = file_get_contents($request->file('file'));
        $json_data = json_decode($json_data, true)['products'];
        foreach ($json_data as $data) {
            $product_quantity = $this->calculateQuantity($data['articles']);
            // check articles stocks
            if ($this->checkArticlesStocks($data['name'], $data['articles'])) {
                if (!$this->isProductExist($data['name'])) {
                    $product_data = [
                        'name' => $data['name'],
                        'price' => $data['price'],
                        'quantity' => $product_quantity,
                        'benefit' => (int)$data['price'] * (int)$product_quantity
                    ];
                    $product = Product::create($product_data);
                    if ($product instanceof Product) {
                        foreach ($data['articles'] as $article) {
                            $product->articles()->attach([
                                'id' => $article['id'],
                            ], [
                                'amount' => $article['amount'],
                            ]);
                        }
                    }
                } else {
                    $product = Product::where('name', $data['name'])->first();
                    $product->update([
                        'quantity' => (int)$product['quantity'] + (int)$product_quantity,
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 0,
                    'product' => $data['name'],
                    'msg' => 'The stock of articles is not enough to make this product',
                ]);
            }
        }
        return response()->json([
            'status' => 1,
            'message' => 'Articles added successfully']);
    }

    private function checkArticlesStocks(string $product_name, array $articles): bool
    {
        $product_quantity = 0;
        foreach ($articles as $article) {
            $articleObj = Article::find($article['id']);
            $product_quantity += $articleObj['stock'];
            if ($article['amount'] > $articleObj['stock']) {
                return false;
            }
            $articleObj->update([
                'stock' => $articleObj['stock'] - $article['amount']
            ]);
        }
        return true;
    }

    private function calculateQuantity(array $articles)
    {
        $product_quantity = 0;
        foreach ($articles as $article) {
            $articleObj = Article::find($article['id']);
            $product_quantity += $articleObj['stock'];
        }
        return $product_quantity;
    }

    private function isProductExist(string $name)
    {
        return Product::where('name', $name)->exists();
    }

}
