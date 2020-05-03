@extends('layouts.app')

@section('title')@yield('title')@endsection

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            Oops, something went wrong.
        </div>
        <div class="card-body">
            @isset($request_id)
                <p>Request ID: {{ $request_id }}</p>
            @endisset

            <p>@yield('message')</p>
        </div>
    </div>
</div>
@endsection
