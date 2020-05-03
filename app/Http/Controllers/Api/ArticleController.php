<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $articles = Article::query()->paginate();

        return ArticleResource::collection($articles);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Article  $article
     *
     * @return \App\Http\Resources\ArticleResource
     */
    public function show(Article $article): ArticleResource
    {
        return new ArticleResource($article);
    }
}
