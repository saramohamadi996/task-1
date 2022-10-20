<?php

namespace App\Models\Article;

use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Article extends Model
{
    use HasFactory;



    protected $fillable = [
//        'id',
        'name',
        'stock'
    ];


    /**
     * @return BelongsToMany
     */
    public function products() : BelongsToMany
    {
       return  $this->belongsToMany(Product::class)->withPivot('amount');
    }
}
