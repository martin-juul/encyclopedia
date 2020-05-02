@extends('layouts.app')

@section('title', 'Profiling Report')

@section('content')
<div class="container mb-3">
    <h1>Report</h1>
</div>

<div class="container-fluid">
    <div class="col-6 col-sm-6 col-lg-3">
        <div class="card">
            <h5 class="card-header">Request</h5>
            <div class="card-body">
                <p class="h6">Runtime</p>

                <ul class="list-group">
                    <li class="list-group-item">CPU {{ $runtime['cpu'] / 1000 }} μs</li>
                    <li class="list-group-item">Memory usage {{ human_bytes($runtime['memory_usage']) }}</li>
                    <li class="list-group-item">Peak memory usage {{ human_bytes($runtime['peak_memory_usage']) }}</li>
                    <li class="list-group-item">Total {{ $runtime['walltime'] / 1000 }} μs</li>
                </ul>

                <p class="h6 mt-4">Context</p>

                <table class="table">
                    <tbody>
                    <tr>
                        <td>ID</td>
                        <td><a href="{{ route('statistics.profile.show', ['id' => $id]) }}">{{ $id }}</a></td>
                    </tr>
                    <tr>
                        <td>Time</td>
                        <td>{{ $timestamp }}</td>
                    </tr>
                    @foreach($context as $key => $value)
                        <tr>
                            <td>{{ ucfirst(str_replace('_', ' ', $key)) }}</td>
                            @if($key === 'url')
                                <td><a href="{{ $value }}" target="_blank">{{ $value }}</a></td>
                            @else
                                <td>{{ any_to_string($value) }}</td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
