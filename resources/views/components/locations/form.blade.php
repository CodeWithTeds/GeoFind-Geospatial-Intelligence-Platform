<div class="card bg-dark border-secondary">
    <div class="card-header bg-secondary text-white">
        <i class="fas fa-map-marker-alt me-2"></i>{{ $title ?? 'Location Form' }}
    </div>
    <div class="card-body">
        <form action="{{ $action }}" method="POST">
            @csrf
            @if(isset($method) && $method === 'PUT')
                @method('PUT')
            @endif
            
            <div class="mb-3">
                <label for="name" class="form-label">Location Name</label>
                <input type="text" class="form-control bg-dark text-light border-secondary" id="name" name="name" 
                    value="{{ $location->name ?? old('name') }}" required> 
                @error('name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div> 

            <div class="mb-3">
                <label for="latitude" class="form-label">Latitude</label>
                <input type="number" step="any" class="form-control bg-dark text-light border-secondary" id="latitude" 
                    name="latitude" value="{{ $location->latitude ?? old('latitude') }}" required min="-90" max="90"> 
                @error('latitude')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="longitude" class="form-label">Longitude</label>
                <input type="number" step="any" class="form-control bg-dark text-light border-secondary" id="longitude" 
                    name="longitude" value="{{ $location->longitude ?? old('longitude') }}" required min="-180" max="180"> 
                @error('longitude')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>{{ $submitText ?? 'Save Location' }}
                </button>
                <a href="{{ route('locations.index')}}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div> 