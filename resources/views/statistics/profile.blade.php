@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<div class="container">
    <h1>Profile</h1>
</div>

<div class="container">
    <pre>
        @json($report, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT)
    </pre>
</div>
@endsection
