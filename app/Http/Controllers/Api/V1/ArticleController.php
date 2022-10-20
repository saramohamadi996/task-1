<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Article\StoreJsonRequest;
use App\Http\Resources\Api\V1\Article\ArticleCollection;
use App\Models\Article\Article;
use Illuminate\Http\JsonResponse;

class ArticleController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->apiResponse(
            new ArticleCollection(
                Article::orderBy('stock', 'desc')->paginate(20)
            )
        );
    }

    /**
     * @param StoreJsonRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function storeJson(StoreJsonRequest $request): JsonResponse
    {
        $list = $this->getJsonFile($request->file, 'articles');
        $checkDuplicateArticles = Article::whereIn(
            'id', array_column($list, 'id')
        )->get()
            ->toArray();
        //All articles are new
        if (count($checkDuplicateArticles) == 0) {
            Article::insert($list);
            return $this->apiResponse(true);
        }
        //has duplicate data to request
        $this->insertOrUpdateValues($list, $checkDuplicateArticles);
        return $this->apiResponse(true);
    }

    /**
     * @param array $list
     * @param array $checkDuplicateArticles
     * @return void
     */
    public function insertOrUpdateValues(array $list, array $checkDuplicateArticles): void
    {
        //insert new articles that exists in list
        $diffArticles = array_diff_key($list, $checkDuplicateArticles);
        if (count($diffArticles) > 0) {
            Article::insert($diffArticles);
        }
        //update stock if articles has duplicated!
        $duplicateArticles = array_diff_key($list, $diffArticles);
        foreach ($duplicateArticles as $article) {
            Article::where('id', $article['id'])
                ->increment('stock', $article['stock']);
        }
    }
}
