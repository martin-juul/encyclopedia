@extends('layouts.dashboard')

@section('title', 'Article Overview')

@section('content')
    <h2>Articles</h2>
    <hr>

    <table class="table">
        <thead>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Parent ID</th>
            <th>Comment</th>
            <th>Revision Time</th>
        </tr>
        </thead>

        <tbody>
        @foreach($articles as $article)
            <tr>
                <td>{{ $article->article_id }}</td>
                <td>
                    <a target="_blank" href="{{ route('articles.show', ['title' => $article->title]) }}">
                        {{ $article->title }}
                    </a>
                </td>
                <td>{{ $article->article_parent_id }}</td>
                <td>{{ $article->comment }}</td>
                <td>{{ $article->revision_time }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {!! $articles->links(); !!}
@endsection
