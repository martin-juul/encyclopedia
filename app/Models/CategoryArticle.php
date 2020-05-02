<?php

namespace App\Models;

/**
 * App\Models\CategoryArticle
 *
 * @property string $id
 * @property string $category_id
 * @property string $article_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Article[] $articles
 * @property-read int|null $articles_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Category[] $categories
 * @property-read int|null $categories_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CategoryArticle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CategoryArticle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CategoryArticle query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CategoryArticle whereArticleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CategoryArticle whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CategoryArticle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CategoryArticle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CategoryArticle whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CategoryArticle extends AbstractPivot
{
    protected $fillable = [
        'article_id',
        'category_id',
    ];

    public function articles()
    {
        return $this->belongsToMany(Article::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
}
