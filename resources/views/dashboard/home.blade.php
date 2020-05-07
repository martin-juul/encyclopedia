@extends('layouts.dashboard')

@section('title', __('dashboard.home'))

@section('content')
    <h2>Encyclopedia</h2>
    <hr>

    <div class="row">
        <x-dashboard.card :header="'Overview'">
            <table class="table">
                <thead>
                <tr>
                    <th>Articles</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>{{ $articleCount }}</td>
                </tr>
                </tbody>
            </table>
        </x-dashboard.card>
    </div>
@endsection
