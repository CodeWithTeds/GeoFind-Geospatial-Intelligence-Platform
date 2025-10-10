@extends('layouts.app')

@section('title', 'Location Tracker')

@section('meta-urls')
    @include('components.locations.meta-urls')
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('locations.create') }}" class="btn btn-success">
            <i class="fas fa-plus-circle me-2"></i>Add New Location
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            @include('components.locations.table')
        </div>

        <div class="col-lg-6">
            <h2 class="text-light mb-4">Location Calculations</h2>

            <div class="accordion" id="calculationsAccordion">
                @include('components.locations.geohash-converter')
                @include('components.locations.reverse-geocoding')
                @include('components.locations.distance-calculator')
                @include('components.locations.midpoint-calculator')
                @include('components.locations.triangle-area-calculator')
                @include('components.locations.points-in-radius')
                @include('components.locations.bearing-calculator')
                @include('components.locations.convex-hull')
                @include('components.locations.grid-generator')
                @include('components.locations.heatmap-generator')
                @include('components.locations.cluster-finder')
            </div>
        </div>
    </div>
@endsection
