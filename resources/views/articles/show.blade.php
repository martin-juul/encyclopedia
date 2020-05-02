@extends('layouts.app')

@section('title', $title)

@section('content')
    <div class="container">
        <h1>{{ $title }}</h1>

        <a href="{{ route('articles.show-source', ['title' => $title]) }}"
           target="_blank"
           class="btn btn-light"
           type="button"
        >Show source</a>
    </div>

    <article class="container article">
        {!! $text !!}
    </article>
@endsection
