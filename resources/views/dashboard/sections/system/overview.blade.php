@extends('layouts.dashboard')

@section('title', 'System Overview')

@section('content')
    <h2>System Overview</h2>
    <hr>

    <div class="row">
        <div class="col-xs-12 col-sm-8 col-md-8 col-lg-5">
            <x-dashboard.card :header="'Modules'">
                <x-php-info :section="'modules'"></x-php-info>
            </x-dashboard.card>
        </div>

        <div class="col-xs-12 col-sm-8 col-md-8 col-lg-5">
            <x-dashboard.card :header="'Environment'">
                <x-php-info :section="'environment'"></x-php-info>
            </x-dashboard.card>
        </div>
    </div>

    <hr>

    <div class="row">
        <div class="col-xs-12 col-sm-8 col-md-8 col-lg-5">
            <x-dashboard.card :header="'Local Filesystem'">
                <x-dashboard.system.filesystem/>
            </x-dashboard.card>
        </div>
    </div>
@endsection
