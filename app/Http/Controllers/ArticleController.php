<?php

namespace App\Http\Controllers;

use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Utilities\Parser\WikiText\WikitextParser;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function __construct()
    {
        if (config('profiling.enabled')) {
            $this->middleware('profile');
        }
    }

    public function index(Request $request)
    {
        $orderBy = $request->query('orderBy', 'title');
        $columns = ['title', 'description'];

        if (!\in_array($orderBy, ['title', 'revision_time'], true)) {
            $orderBy = 'title';
        }

        if (!\in_array($orderBy, $columns, true)) {
            $columns += [$orderBy];
        }

        $articles = Article::query()
            ->orderBy('title')
            ->select($columns)
            ->paginate(22);

        return view('articles.index', [
            'articles' => $articles,
        ]);
    }

    public function show(Request $request, string $title)
    {
        $article = Article::whereTitle($title)->firstOrFail();

        $parser = new WikitextParser($article->text);

        return view('articles.show', [
            'title' => $article->title,
            'text'  => $parser->result,
        ]);
    }

    public function showSource(Request $request, string $title)
    {
        $article = Article::whereTitle($title)->firstOrFail();

        $parser = new WikitextParser($article->text);

        return view('articles.source', [
            'title'        => $article->title,
            'source'       => $article->text,
            'preprocessed' => $parser->preprocessed,
        ]);
    }

    public function searchApi(Request $request)
    {
        $results = Article::search($request->query('q'))->paginate();

        return ArticleResource::collection($results);
    }

    public function searchView(Request $request)
    {
        $articles = $this->searchApi($request);

        return view('articles.search', [
            'query'    => $request->query('q'),
            'articles' => $articles,
        ]);
    }
}
