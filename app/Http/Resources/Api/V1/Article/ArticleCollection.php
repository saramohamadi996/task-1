<?php

namespace App\Http\Resources\Api\V1\Article;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ArticleCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            $this->collection->map(function ($article) {
                return [
                    'id' => $article->id,
                    'name' => $article->name,
                    'stock' => $article->stock
                ];
            })
        ];
    }
}
