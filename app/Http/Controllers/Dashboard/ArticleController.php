<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Article;

class ArticleController extends Controller
{
    public function showOverviewPage()
    {
        $articles = Article::select([
            'article_id',
            'article_parent_id',
            'title',
            'comment',
            'revision_time',
        ])->latest('revision_time')->paginate(18);

        return view('dashboard.sections.article.overview', [
            'articles' => $articles,
        ]);
    }
}
