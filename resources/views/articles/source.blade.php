@extends('layouts.app')

@section('title', "Source | $title")

@section('content')
    <div class="container">
        <h1>Source for article {{ $title }}</h1>
    </div>

    <div class="container-fluid">
        <hr>
    </div>

    <div class="container-fluid mt-4 d-flex justify-content-between">
        <div class="container-fluid" style="width: 50%;">
            <h2>Preprocessed</h2>
            <br/>

            <pre>{!! $preprocessed !!}</pre>
        </div>

        <div class="container-fluid" style="width: 50%;">
            <h2>Source</h2>
            <br/>

            <pre>{!! $source !!}</pre>
        </div>
    </div>
@endsection
