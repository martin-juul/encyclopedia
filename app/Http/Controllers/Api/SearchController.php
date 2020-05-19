<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function articles(Request $request)
    {
        $results = Article::search($request->query('q'))->paginate();

        return ArticleResource::collection($results);
    }

}
