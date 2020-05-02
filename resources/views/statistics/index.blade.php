@extends('layouts.app')

@section('title', 'Statistics')

@section('content')
    <div class="container">
        <h1>Statistics</h1>
    </div>

    <div class="container">
        <div class="card">
            <h5 class="card-header">Database</h5>

            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th scope="col">Table</th>
                        <th scope="col">Size</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($dbtables as $table)
                        <tr>
                            <td>{{ $table['table'] }}</th>
                            <td>{{ $table['size'] }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <div class="card">
            <h5 class="card-header">Routes</h5>

            <div class="card-body">
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">URI</th>
                        <th scope="col">Methods</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($routes as $name => $route)
                        <tr>
                            <td>{{ $name }}</td>
                            <td>{{ $route->uri }}</td>
                            <td>{{ implode(', ', $route->methods) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
