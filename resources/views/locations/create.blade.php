@extends('layouts.admin')

@section('title', 'Add New Location')

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8 mx-auto">
            @include('components.locations.form', [
                'action' => route('admin.locations.store'),
                'title' => 'Add New Location',
                'submitText' => 'Save Location'
            ])
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
@endsection