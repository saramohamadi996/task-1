<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\ArticleCollection;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticlesController extends Controller
{
    public function index()
    {
        $articles = Article::paginate();
        return new ArticleCollection($articles);
    }

    public function import(Request $request)
    {
        $json_data = file_get_contents($request->file('file'));
        $json_data = json_decode($json_data, true)['articles'];
        foreach ($json_data as $data) {
            if (!$this->isArticleExist($data['name'])) {
                $article_data = [
                    'id' => $data['id'],
                    'name' => $data['name'],
                    'stock' => (int)$data['stock']
                ];
                $article = Article::create($article_data);
            } else {
                $article = Article::where('name', $data['name'])->first();
                $article->update([
                    'stock' => (int)$article['stock'] + (int)$data['stock'],
                ]);
            }

        }

        return response()->json([
            'status' => 1,
            'message' => 'Articles added successfully']);
    }


    private function isArticleExist(string $name)
    {
        return Article::where('name', $name)->exists();
    }
}
