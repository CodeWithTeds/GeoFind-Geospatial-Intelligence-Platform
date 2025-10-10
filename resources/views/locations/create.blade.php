@extends('layouts.app')

@section('title', 'Add New Location')

@section('content')
    <div class="row">
        <div class="col-md-8 mx-auto">
            @include('components.locations.form', [
                'action' => route('locations.store'),
                'title' => 'Add New Location',
                'submitText' => 'Save Location'
            ])
        </div>
    </div>
@endsection