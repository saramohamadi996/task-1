<?php

namespace App\Models\Product;

use App\Models\Article\Article;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;


    protected $fillable = [
//        'id',
        'name',
        'price',
        'available_stock',
    ];


    /**
     * @return BelongsToMany
     */
    public function articles() : BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_product')
                    ->orderBy('article_product.amount', 'desc')
            ->withPivot(['amount']);
    }
}
