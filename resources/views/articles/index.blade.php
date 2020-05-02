@extends('layouts.app')

@section('title', 'Browse')

@section('content')
    <div class="container">
        <h1>Articles <span class="text-muted">({{ $articles->total() }})</span></h1>

        <ul class="list-group">
            @foreach($articles as $article)
                <li class="list-group-item">
                    <a href="{{ route('articles.show', ['title' => $article->title]) }}">
                        <span>{{ $article->title }}</span>
                    </a>

                    <p>{{ $article->description }}</p>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="container mt-4">
        {!! $articles->render() !!}
    </div>
@endsection
