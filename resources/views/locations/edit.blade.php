@extends('layouts.app')

@section('title', 'Edit Location')

@section('content')
    <div class="row">
        <div class="col-md-8 mx-auto">
            @include('components.locations.form', [
                'action' => route('locations.update', $location),
                'method' => 'PUT',
                'location' => $location,
                'title' => 'Edit Location',
                'submitText' => 'Update Location'
            ])
        </div>
    </div>
@endsection