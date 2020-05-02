@extends('layouts.app')

@section('title', 'Search')

@section('content')
<div class="container">
    <h1>
        {{ __('search.showing_results_for') }}
        {{ __('symbols.quotation_mark.start') . $query . __('symbols.quotation_mark.end') }}
    </h1>
</div>

<div class="container">
@foreach($articles as $article)
    <div class="card">
        <div class="card-body">
            <a href="{{ route('articles.show', ['title' => $article->title]) }}">
                <h5 class="card-title">{{ $article->title }}</h5>
            </a>

            <p class="card-text">{{ $article->description }}</p>
        </div>
    </div>
@endforeach
</div>

<div class="container mt-4">
    {!! $articles->render() !!}
</div>
@endsection
