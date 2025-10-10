<div class="accordion-item bg-dark text-light border-secondary mb-3">
    <h2 class="accordion-header">
        <button class="accordion-button bg-secondary text-white collapsed" type="button" data-bs-toggle="collapse"
            data-bs-target="#geohashCollapse" aria-expanded="false" aria-controls="geohashCollapse">
            <i class="fas fa-hashtag me-2"></i>Convert to Geohash
        </button>
    </h2>
    <div id="geohashCollapse" class="accordion-collapse collapse" data-bs-parent="#calculationsAccordion">
        <div class="accordion-body">
            <form id="geohashForm">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="latitude" class="form-label">Latitude</label>
                        <input type="number" step="any" class="form-control bg-dark text-light border-secondary" 
                            id="latitude" name="latitude" required min="-90" max="90"
                            placeholder="Enter latitude (-90 to 90)">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="longitude" class="form-label">Longitude</label>
                        <input type="number" step="any" class="form-control bg-dark text-light border-secondary" 
                            id="longitude" name="longitude" required min="-180" max="180"
                            placeholder="Enter longitude (-180 to 180)">
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary" id="convertBtn">
                        <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true"></span>
                        <i class="fas fa-exchange-alt me-2"></i>Convert
                    </button>
                    <button type="button" class="btn btn-info" id="showMapBtn" style="display: none;">
                        <i class="fas fa-map me-2"></i>Show Map
                    </button>
                </div>
            </form>
            <div id="geohashResult" class="mt-3 p-3 border border-secondary rounded"></div>
                
            <div id="geohashMap" class="mt-3 map-container" style="height: 350px; display: none;"> Love you</div>
        
        </div>
    </div>
</div> 


@push('scripts')
<script src="{{ asset('js/geohash-map.js') }}"></script>    
@endpush